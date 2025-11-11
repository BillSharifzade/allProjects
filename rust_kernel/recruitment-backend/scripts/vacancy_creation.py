"""Automate vacancy creation workflow in Koinoti Nav admin panel.

This script logs into the admin panel, navigates to the vacancies section,
and clicks the "Добавить" (Add) button. Further steps (filling the vacancy
form, uploading assets, etc.) can build on top of the `open_add_vacancy_form`
function later.

Usage example:

    python vacancy_creation.py --email admin@job.koinotinav.tj 
        --password 3cbdwACV --headless

Requirements:
    - Selenium (`pip install selenium`)
    - A matching WebDriver executable on PATH (e.g., chromedriver)

Environment variables:
    VACANCY_PANEL_EMAIL    Optional, overrides --email argument if set
    VACANCY_PANEL_PASSWORD Optional, overrides --password argument if set
"""

from __future__ import annotations

import argparse
import os
import sys
from dataclasses import dataclass
from typing import Optional

from selenium import webdriver
from selenium.common.exceptions import TimeoutException
from selenium.webdriver.chrome.options import Options as ChromeOptions
from selenium.webdriver.common.by import By
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.support.ui import WebDriverWait


LOGIN_URL = "https://job.koinotinav.tj/dashboard"
VACANCIES_PATH = "/dashboard/vacancies"
WAIT_TIMEOUT = 20


@dataclass
class ScriptConfig:
    """Runtime configuration for the automation script."""

    email: str
    password: str
    headless: bool = False
    driver_binary: Optional[str] = None
    vacancy: Optional["VacancyFormData"] = None
    delete_mode: bool = False
    vacancy_id: Optional[str] = None


@dataclass
class VacancyFormData:
    """Payload representing the vacancy being created."""

    title: str
    content: str
    city: Optional[str] = None
    direction: Optional[str] = None
    company: Optional[str] = None
    hot: bool = False


def build_driver(config: ScriptConfig) -> webdriver.Chrome:
    """Create and return a configured Chrome WebDriver instance."""

    options = ChromeOptions()
    if config.headless:
        options.add_argument("--headless=new")
    options.add_argument("--window-size=1280,900")
    options.add_argument("--disable-gpu")
    options.add_argument("--no-sandbox")

    if config.driver_binary:
        options.binary_location = config.driver_binary

    try:
        driver = webdriver.Chrome(options=options)
    except Exception as exc:  # pragma: no cover - setup errors depend on host
        raise RuntimeError(
            "Failed to instantiate Chrome WebDriver. Ensure chromedriver is "
            "installed and accessible."
        ) from exc

    return driver


def login(driver: webdriver.Chrome, config: ScriptConfig) -> None:
    """Authenticate into the admin panel."""

    driver.get(LOGIN_URL)

    wait = WebDriverWait(driver, WAIT_TIMEOUT)
    email_input = wait.until(EC.presence_of_element_located((By.NAME, "email")))
    password_input = wait.until(
        EC.presence_of_element_located((By.NAME, "password"))
    )

    email_input.clear()
    email_input.send_keys(config.email)
    password_input.clear()
    password_input.send_keys(config.password)

    submit_button = driver.find_element(By.CSS_SELECTOR, "form button[type='submit']")
    submit_button.click()

    # Confirm login by waiting for the sidebar navigation to render.
    wait.until(EC.presence_of_element_located((By.CSS_SELECTOR, "aside nav")))


def navigate_to_vacancies(driver: webdriver.Chrome) -> None:
    """Open the vacancies list via the sidebar navigation."""

    wait = WebDriverWait(driver, WAIT_TIMEOUT)
    vacancies_link = wait.until(
        EC.element_to_be_clickable((By.CSS_SELECTOR, "a[href='" + VACANCIES_PATH + "']"))
    )

    vacancies_link.click()

    wait.until(EC.url_contains(VACANCIES_PATH))
    wait.until(
        EC.presence_of_element_located(
            (By.XPATH, "//h1[contains(normalize-space(), 'Вакансии')]")
        )
    )


def open_add_vacancy_form(driver: webdriver.Chrome) -> None:
    """Click the "Добавить" button on the vacancies page."""

    wait = WebDriverWait(driver, WAIT_TIMEOUT)
    add_button = wait.until(
        EC.element_to_be_clickable((By.XPATH, "//button[contains(., 'Добавить')]")
        )
    )

    add_button.click()


def fill_vacancy_form(driver: webdriver.Chrome, vacancy: VacancyFormData) -> None:
    """Populate the vacancy creation form and submit it."""

    wait = WebDriverWait(driver, WAIT_TIMEOUT)

    wait.until(
        EC.presence_of_element_located(
            (By.XPATH, "//h1[contains(normalize-space(), 'Добавить вакансию')]")
        )
    )

    def populate_rich_text(label_text: str, value: str) -> None:
        editor = wait.until(
            EC.element_to_be_clickable(
                (
                    By.XPATH,
                    f"//label[contains(., '{label_text}')]/following::div[@contenteditable='true'][1]",
                )
            )
        )
        editor.click()
        editor.send_keys(Keys.CONTROL, "a")
        editor.send_keys(Keys.DELETE)
        editor.send_keys(value)

    populate_rich_text("Заголовок", vacancy.title)
    populate_rich_text("Содержание", vacancy.content)

    def fill_text_input(name: str, value: Optional[str]) -> None:
        if value is None:
            return
        field = wait.until(EC.element_to_be_clickable((By.NAME, name)))
        field.clear()
        field.send_keys(value)

    fill_text_input("city", vacancy.city)
    fill_text_input("direction", vacancy.direction)

    if vacancy.company:
        company_button = wait.until(
            EC.element_to_be_clickable(
                (
                    By.XPATH,
                    "//label[contains(., 'Компания')]/following::button[1]",
                )
            )
        )
        company_button.click()
        option = wait.until(
            EC.element_to_be_clickable(
                (
                    By.XPATH,
                    f"//div[contains(@class, 'shadow-sm')]//button[normalize-space()='{vacancy.company}']",
                )
            )
        )
        option.click()

    if vacancy.hot:
        hot_checkbox = wait.until(
            EC.presence_of_element_located(
                (By.XPATH, "//label[contains(., 'Горячая вакансия')]/input")
            )
        )
        if not hot_checkbox.is_selected():
            hot_checkbox.click()

    submit_button = wait.until(
        EC.element_to_be_clickable(
            (By.XPATH, "//button[@type='submit' and contains(., 'Добавить')]")
        )
    )
    submit_button.click()


def delete_vacancy(driver: webdriver.Chrome, config: ScriptConfig) -> None:
    """Delete an existing vacancy by ID using the admin interface."""

    if not config.vacancy_id:
        raise ValueError("vacancy_id is required to delete a vacancy")

    wait = WebDriverWait(driver, WAIT_TIMEOUT)

    # Focus the search box and filter by the provided ID
    search_input = wait.until(
        EC.element_to_be_clickable((By.CSS_SELECTOR, "input[type='search']"))
    )
    search_input.clear()
    search_input.send_keys(config.vacancy_id)
    search_input.send_keys(Keys.ENTER)

    # Locate the table row matching the vacancy ID
    row_xpath = (
        "//table//tbody//tr[td[1][normalize-space()="
        f"'{config.vacancy_id}'"  # noqa: E131
        "]]"
    )
    row = wait.until(EC.presence_of_element_located((By.XPATH, row_xpath)))

    delete_button = wait.until(
        EC.element_to_be_clickable(
            (By.XPATH, row_xpath + "//button[contains(@class, 'bg-red-500')]")
        )
    )
    delete_button.click()

    # Confirm deletion in the modal dialog
    confirm_button = wait.until(
        EC.element_to_be_clickable(
            (
                By.XPATH,
                "//form//button[@type='submit' and contains(normalize-space(), 'Удалить')]",
            )
        )
    )
    confirm_button.click()

    # Wait until the row disappears from the table
    wait.until(EC.staleness_of(row))
    print(f"Vacancy {config.vacancy_id} deleted successfully.")


def build_config_from_kwargs(**kwargs) -> ScriptConfig:
    """Create ScriptConfig directly from keyword arguments (for integration)."""

    delete_mode = kwargs.pop("delete_mode", False)
    vacancy_id = kwargs.pop("vacancy_id", None)
    vacancy_data = kwargs.pop("vacancy", None)

    if delete_mode:
        if not vacancy_id:
            raise ValueError("vacancy_id is required in delete mode")
        return ScriptConfig(vacancy=None, delete_mode=True, vacancy_id=vacancy_id, **kwargs)

    if vacancy_data is None:
        vacancy_data = VacancyFormData(
            title=kwargs.pop("title"),
            content=kwargs.pop("content"),
            city=kwargs.pop("city", None),
            direction=kwargs.pop("direction", None),
            company=kwargs.pop("company", None),
            hot=kwargs.pop("hot", False),
        )

    return ScriptConfig(vacancy=vacancy_data, delete_mode=False, vacancy_id=None, **kwargs)


def build_config_from_env(env: Optional[dict[str, str]] = None) -> ScriptConfig:
    """Create ScriptConfig from environment variables."""

    env = env or os.environ
    hot_env = env.get("VACANCY_HOT", "")
    hot = hot_env.lower() in {"1", "true", "yes"} if hot_env else False
    delete_flag = env.get("VACANCY_DELETE", "").lower() in {"1", "true", "yes"}
    vacancy_id = env.get("VACANCY_ID")

    common_kwargs = dict(
        email=env.get("VACANCY_PANEL_EMAIL", ""),
        password=env.get("VACANCY_PANEL_PASSWORD", ""),
        headless=env.get("VACANCY_HEADLESS", "").lower() in {"1", "true", "yes"},
        driver_binary=env.get("VACANCY_CHROME_BINARY"),
    )

    if delete_flag:
        if not vacancy_id:
            raise ValueError("VACANCY_ID must be provided when VACANCY_DELETE is enabled")
        return ScriptConfig(
            delete_mode=True,
            vacancy_id=vacancy_id,
            vacancy=None,
            **common_kwargs,
        )

    return ScriptConfig(
        delete_mode=False,
        vacancy_id=None,
        vacancy=VacancyFormData(
            title=env.get("VACANCY_TITLE", ""),
            content=env.get("VACANCY_CONTENT", ""),
            city=env.get("VACANCY_CITY"),
            direction=env.get("VACANCY_DIRECTION"),
            company=env.get("VACANCY_COMPANY"),
            hot=hot,
        ),
        **common_kwargs,
    )


def parse_args(argv: list[str]) -> ScriptConfig:
    """Parse CLI arguments into a ScriptConfig."""

    parser = argparse.ArgumentParser(description="Automate vacancy creation flow")
    parser.add_argument("--email", help="Admin panel login email")
    parser.add_argument("--password", help="Admin panel login password")
    parser.add_argument(
        "--headless",
        action="store_true",
        help="Run Chrome in headless mode",
    )
    parser.add_argument(
        "--chrome-binary",
        help="Optional path to Chrome/Chromium binary",
    )
    parser.add_argument("--title", help="Vacancy title text")
    parser.add_argument("--content", help="Vacancy content/description")
    parser.add_argument("--city", help="Vacancy city")
    parser.add_argument("--direction", help="Vacancy direction/category")
    parser.add_argument("--company", help="Company to associate with vacancy")
    parser.add_argument(
        "--hot",
        action="store_true",
        help="Mark the vacancy as hot",
    )
    parser.add_argument(
        "--delete",
        action="store_true",
        help="Delete vacancy instead of creating a new one",
    )
    parser.add_argument(
        "--vacancy-id",
        help="Vacancy ID to delete (required with --delete)",
    )

    args = parser.parse_args(argv)

    email = os.getenv("VACANCY_PANEL_EMAIL") or args.email
    password = os.getenv("VACANCY_PANEL_PASSWORD") or args.password

    delete_mode = args.delete or (os.getenv("VACANCY_DELETE", "").lower() in {"1", "true", "yes"})
    vacancy_id = args.vacancy_id or os.getenv("VACANCY_ID")

    title = os.getenv("VACANCY_TITLE") or args.title
    content = os.getenv("VACANCY_CONTENT") or args.content
    city = os.getenv("VACANCY_CITY") or args.city
    direction = os.getenv("VACANCY_DIRECTION") or args.direction
    company = os.getenv("VACANCY_COMPANY") or args.company
    hot_env = os.getenv("VACANCY_HOT")
    hot = args.hot or (hot_env.lower() in {"1", "true", "yes"} if hot_env else False)

    if not email or not password:
        parser.error(
            "Credentials are required. Provide --email/--password or set "
            "VACANCY_PANEL_EMAIL/VACANCY_PANEL_PASSWORD environment variables."
        )

    if delete_mode:
        if not vacancy_id:
            parser.error("--vacancy-id is required when using --delete")
        return ScriptConfig(
            email=email,
            password=password,
            headless=args.headless,
            driver_binary=args.chrome_binary,
            delete_mode=True,
            vacancy_id=vacancy_id,
            vacancy=None,
        )

    if not title or not content:
        parser.error(
            "Vacancy title and content are required. Provide --title/--content or "
            "set VACANCY_TITLE/VACANCY_CONTENT environment variables."
        )

    return ScriptConfig(
        email=email,
        password=password,
        headless=args.headless,
        driver_binary=args.chrome_binary,
        vacancy=VacancyFormData(
            title=title,
            content=content,
            city=city,
            direction=direction,
            company=company,
            hot=hot,
        ),
        delete_mode=False,
        vacancy_id=None,
    )


def run_vacancy_creation(config: ScriptConfig) -> int:
    """Execute the full vacancy creation workflow with the provided config."""

    driver: Optional[webdriver.Chrome] = None

    try:
        driver = build_driver(config)
        login(driver, config)
        navigate_to_vacancies(driver)
        if config.delete_mode:
            delete_vacancy(driver, config)
        else:
            open_add_vacancy_form(driver)
            if config.vacancy is None:
                raise ValueError("Vacancy data must be provided when not in delete mode")
            fill_vacancy_form(driver, config.vacancy)
            print("Successfully opened the vacancy creation form.")
        return 0
    except TimeoutException as exc:
        print("Timed out while waiting for the page to load:", exc)
        return 1
    except Exception as exc:  # pragma: no cover - runtime issues differ per host
        print("Automation failed:", exc)
        return 1
    finally:
        if driver is not None:
            driver.quit()


def run_with_payload(payload: dict, *, headless: bool = True) -> int:
    """Create ScriptConfig from a raw payload (e.g., 1F feed) and run the script."""

    delete_mode = payload.get("delete", False)

    base_kwargs = dict(
        email=payload["email"],
        password=payload["password"],
        headless=headless,
        driver_binary=payload.get("driver_binary"),
    )

    if delete_mode:
        vacancy_id = payload.get("vacancy_id")
        if not vacancy_id:
            raise ValueError("vacancy_id is required when delete=true")
        config = build_config_from_kwargs(
            delete_mode=True,
            vacancy_id=vacancy_id,
            **base_kwargs,
        )
    else:
        required = ["title", "content"]
        missing = [field for field in required if not payload.get(field)]
        if missing:
            raise ValueError(f"Missing required vacancy fields: {', '.join(missing)}")
        config = build_config_from_kwargs(
            delete_mode=False,
            vacancy_id=None,
            title=payload["title"],
            content=payload["content"],
            city=payload.get("city"),
            direction=payload.get("direction"),
            company=payload.get("company"),
            hot=payload.get("hot", False),
            **base_kwargs,
        )

    return run_vacancy_creation(config)


def main(argv: list[str] = None) -> int:
    config = parse_args(argv or sys.argv[1:])
    return run_vacancy_creation(config)


if __name__ == "__main__":
    raise SystemExit(main())
