$(document).ready(function(){
	if ($('.theme-container-primary').length > 0) {

		//switcher color pickers
		const pickrContainerPrimary = document.querySelector(
			".pickr-container-primary"
		);
		const themeContainerPrimary = document.querySelector(
			".theme-container-primary"
		);
		const pickrContainerBackground = document.querySelector(
			".pickr-container-background"
		);
		const themeContainerBackground = document.querySelector(
			".theme-container-background"
		);
		const pickrtopbar = document.querySelector(
			".pickr-topbar"
		);
		const themetopbar = document.querySelector(
			".theme-topbar"
		);
		const pickrtopbarcolor = document.querySelector(
			".pickr-topbarcolor"
		);
		const themetopbarcolor = document.querySelector(
			".theme-topbarcolor"
		);

		/* for theme primary */
		const nanoThemes = [
			[
			"nano",
			{
				defaultRepresentation: "RGB",
				components: {
				preview: true,
				opacity: false,
				hue: true,
		
				interaction: {
					hex: false,
					rgba: true,
					hsva: false,
					input: true,
					clear: false,
					save: false,
				},
				},
			},
			],
		];
		const nanoButtons = [];
		let nanoPickr = null;
		for (const [theme, config] of nanoThemes) {
			const button = document.createElement("button");
			button.innerHTML = theme;
			nanoButtons.push(button);
		
			button.addEventListener("click", () => {
			const el = document.createElement("p");
			pickrContainerPrimary.appendChild(el);
		
			/* Delete previous instance */
			if (nanoPickr) {
				nanoPickr.destroyAndRemove();
			}
		
			/* Apply active class */
			for (const btn of nanoButtons) {
				btn.classList[btn === button ? "add" : "remove"]("active");
			}
		
			/* Create fresh instance */
			nanoPickr = new Pickr(
				Object.assign(
				{
					el,
					theme,
					default: "#546dfe",
				},
				config
				)
			);
		
			/* Set events */
			nanoPickr.on("changestop", (source, instance) => {
				let color = instance.getColor().toRGBA();
				let html = document.querySelector("html");
				html.style.setProperty(
				"--primary-rgb",
				`${Math.floor(color[0])}, ${Math.floor(color[1])}, ${Math.floor(
					color[2]
				)}`
				);
				/* theme color picker */
				localStorage.setItem(
				"primaryRGB",
				`${Math.floor(color[0])}, ${Math.floor(color[1])}, ${Math.floor(
					color[2]
				)}`
				);
				localStorage.removeItem('color');
				// updateColors();
			});
			});
		
			themeContainerPrimary.appendChild(button);
		}
		nanoButtons[0].click();

		// for theme background 
		const nanoThemes1 = [
			[
			"nano",
			{
				defaultRepresentation: "RGB",
				components: {
				preview: true,
				opacity: false,
				hue: true,
		
				interaction: {
					hex: false,
					rgba: true,
					hsva: false,
					input: true,
					clear: false,
					save: false,
				},
				},
			},
			],
		];
		const nanoButtons1 = [];
		let nanoPickr1 = null;
		for (const [theme, config] of nanoThemes) {
			const button = document.createElement("button");
			button.innerHTML = theme;
			nanoButtons1.push(button);
		
			button.addEventListener("click", () => {
			const el = document.createElement("p");
			pickrContainerBackground.appendChild(el);
		
			/* Delete previous instance */
			if (nanoPickr1) {
				nanoPickr1.destroyAndRemove();
			}
		
			/* Apply active class */
			for (const btn of nanoButtons) {
				btn.classList[btn === button ? "add" : "remove"]("active");
			}
		
			/* Create fresh instance */
			nanoPickr1 = new Pickr(
				Object.assign(
				{
					el,
					theme,
					default: "#546dfe",
				},
				config
				)
			);
		
			/* Set events */
			nanoPickr1.on("changestop", (source, instance) => {
				let color = instance.getColor().toRGBA();
				let html = document.querySelector("html");
				html.style.setProperty(
				"--sidebar-rgb",
				`${Math.floor(color[0])}, ${Math.floor(color[1])}, ${Math.floor(
					color[2]
				)}`
				);
				// updateColors();
				localStorage.setItem(
				"sidebarRGB",
				`${Math.floor(color[0])}, ${Math.floor(color[1])}, ${Math.floor(
					color[2]
				)}`
				);
				localStorage.removeItem('sidebarTheme');
			});
			});
			themeContainerBackground.appendChild(button);
		}
		nanoButtons1[0].click();

		// for theme Topbar
		const nanoThemes2 = [
			[
			"nano",
			{
				defaultRepresentation: "RGB",
				components: {
				preview: true,
				opacity: false,
				hue: true,
		
				interaction: {
					hex: false,
					rgba: true,
					hsva: false,
					input: true,
					clear: false,
					save: false,
				},
				},
			},
			],
		];
		const nanoButtons2 = [];
		let nanoPickr2 = null;
		for (const [theme, config] of nanoThemes) {
			const button = document.createElement("button");
			button.innerHTML = theme;
			nanoButtons2.push(button);
		
			button.addEventListener("click", () => {
			const el = document.createElement("p");
			pickrtopbar.appendChild(el);
		
			/* Delete previous instance */
			if (nanoPickr2) {
				nanoPickr2.destroyAndRemove();
			}
		
			/* Apply active class */
			for (const btn of nanoButtons) {
				btn.classList[btn === button ? "add" : "remove"]("active");
			}
		
			/* Create fresh instance */
			nanoPickr2 = new Pickr(
				Object.assign(
				{
					el,
					theme,
					default: "#546dfe",
				},
				config
				)
			);
		
			/* Set events */
			nanoPickr2.on("changestop", (source, instance) => {
				let color = instance.getColor().toRGBA();
				let html = document.querySelector("html");
				html.style.setProperty(
				"--topbar-rgb",
				`${Math.floor(color[0])}, ${Math.floor(color[1])}, ${Math.floor(
					color[2]
				)}`
				);
				// updateColors();
				localStorage.setItem(
				"topbarRGB",
				`${Math.floor(color[0])}, ${Math.floor(color[1])}, ${Math.floor(
					color[2]
				)}`
				);
				localStorage.removeItem('topbar');
			});
			});
			themetopbar.appendChild(button)
		}
		nanoButtons2[0].click();

		/* for theme topbar overlay */
		const nanoThemes3 = [
			[
			"nano",
			{
				defaultRepresentation: "RGB",
				components: {
				preview: true,
				opacity: false,
				hue: true,
		
				interaction: {
					hex: false,
					rgba: true,
					hsva: false,
					input: true,
					clear: false,
					save: false,
				},
				},
			},
			],
		];
		const nanoButtons3 = [];
		let nanoPickr3 = null;
		for (const [theme, config] of nanoThemes) {
			const button = document.createElement("button");
			button.innerHTML = theme;
			nanoButtons3.push(button);
		
			button.addEventListener("click", () => {
			const el = document.createElement("p");
			pickrtopbarcolor.appendChild(el);
		
			/* Delete previous instance */
			if (nanoPickr3) {
				nanoPickr3.destroyAndRemove();
			}
		
			/* Apply active class */
			for (const btn of nanoButtons) {
				btn.classList[btn === button ? "add" : "remove"]("active");
			}
		
			/* Create fresh instance */
			nanoPickr3 = new Pickr(
				Object.assign(
				{
					el,
					theme,
					default: "#546dfe",
				},
				config
				)
			);
		
			/* Set events */
			nanoPickr3.on("changestop", (source, instance) => {
				let color = instance.getColor().toRGBA();
				let html = document.querySelector("html");
				html.style.setProperty(
				"--topbarcolor-rgb",
				`${Math.floor(color[0])}, ${Math.floor(color[1])}, ${Math.floor(
					color[2]
				)}`
				);
				/* theme color picker */
				localStorage.setItem(
				"topbarcolorRGB",
				`${Math.floor(color[0])}, ${Math.floor(color[1])}, ${Math.floor(
					color[2]
				)}`
				);
				localStorage.removeItem('topbarcolor');
				// updateColors();
			});
			});
		
			themetopbarcolor.appendChild(button);
		}
		nanoButtons3[0].click();

		function localStorageBackup() {
			// if there is a value stored, update color picker and background color
			// Used to retrive the data from local storage
			if (localStorage.primaryRGB) {
			if (document.querySelector(".theme-container-primary")) {
				document.querySelector(".theme-container-primary").value =
				localStorage.primaryRGB;
			}
			document
				.querySelector("html")
				.style.setProperty("--primary-rgb", localStorage.primaryRGB);
			}

			if (localStorage.sidebarRGB) {
				if (document.querySelector(".theme-container-background")) {
				document.querySelector(".theme-container-background").value =
					localStorage.sidebarRGB;
				}
				document
				.querySelector("html")
				.style.setProperty("--sidebar-rgb", localStorage.sidebarRGB);
			}

			if (localStorage.topbarRGB) {
				if (document.querySelector(".theme-topbar")) {
				document.querySelector(".theme-topbar").value =
					localStorage.topbarRGB;
				}
				document
				.querySelector("html")
				.style.setProperty("--topbar-rgb", localStorage.topbarRGB);
			}

			if (localStorage.topbarcolorRGB) {
				if (document.querySelector(".theme-topbarcolor")) {
				document.querySelector(".theme-topbarcolor").value =
					localStorage.topbarcolorRGB;
				}
				document
				.querySelector("html")
				.style.setProperty("--topbarcolor-rgb", localStorage.topbarcolorRGB);
			}
		}
		localStorageBackup();

		document.querySelectorAll('[data-color="all"]').forEach(function(el) {
			const associatedInput = el.querySelector('input[name="color"]');
			associatedInput.checked = false;
		})

		document.querySelectorAll('[data-sidebar="all"]').forEach(function(el) {
			const associatedInput = el.querySelector('input[name="sidebar"]');
			associatedInput.checked = false;
		})

		document.querySelectorAll('[data-topbar="all"]').forEach(function(el) {
			const associatedInput = el.querySelector('input[name="topbar"]');
			associatedInput.checked = false;
		})

		document.querySelectorAll('[data-topbarcolor="all"]').forEach(function(el) {
			const associatedInput = el.querySelector('input[name="topbarcolor"]');
			associatedInput.checked = false;
		})

		// Function to uncheck all radio buttons
		function uncheckDataColorRadioButtons() {
			const radioButtons  = document.querySelectorAll('input[name="color"]');
			radioButtons.forEach(function(radio) {
				radio.checked = false; // Uncheck each radio button
			});
		}

	function uncheckSideBarRadioButtons() {
		const radioButtons  = document.querySelectorAll('input[name="sidebar"]');
		radioButtons.forEach(function(radio) {
			radio.checked = false; // Uncheck each radio button
		});
	}

	function uncheckTopBarRadioButtons() {
		const radioButtons  = document.querySelectorAll('input[name="topbar"]');
		radioButtons.forEach(function(radio) {
			radio.checked = false; // Uncheck each radio button
		});
	}

		function uncheckTopBarColorRadioButtons() {
			const radioButtons  = document.querySelectorAll('input[name="topbarcolor"]');
			radioButtons.forEach(function(radio) {
				radio.checked = false; // Uncheck each radio button
			});
		}

		document.querySelectorAll('.pickr-container-primary').forEach(function(element) {
			element.addEventListener('click', function() {
				// Update the 'data-color' attribute value
				document.querySelector("html").setAttribute('data-color', 'all');
				uncheckDataColorRadioButtons();
			});
		});

		document.querySelectorAll('.pickr-container-background').forEach(function(element) {
			element.addEventListener('click', function() {
				document.querySelector("html").setAttribute('data-sidebar', 'all');
				uncheckSideBarRadioButtons();
			});
		});

		document.querySelectorAll('.pickr-topbar').forEach(function(element) {
			element.addEventListener('click', function() {
				document.querySelector("html").setAttribute('data-topbar', 'all');
				uncheckTopBarRadioButtons();
			});
		});

		document.querySelectorAll('.pickr-topbarcolor').forEach(function(element) {
			element.addEventListener('click', function() {
				document.querySelector("html").setAttribute('data-topbarcolor', 'all');
				uncheckTopBarColorRadioButtons();
			});
		});
	}
});