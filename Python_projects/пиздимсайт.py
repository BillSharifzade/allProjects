from pywebcopy import save_website

save_website(
url="https://rhodeskin.com",
project_folder="C:\parsedWebsite",
project_name="rhodeskin",
bypass_robots=True,
debug=True,
open_in_browser=True,
delay=None,
threaded=False,
)