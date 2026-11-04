/// <reference types="vite/client" />

/*
 * <p5-sketch> — switches a sketch between its code and what it draws.
 *
 * The markup comes from views/elements/x-sketch.view.php: a hidden switch, the
 * code panel, an empty render panel. Without this script the figure is a code
 * block; with it, the switch appears and the two panels become tabs.
 *
 * The code that runs is the text of the <pre>, read back at run time. There is
 * no second copy anywhere, so the page cannot show one sketch and run another.
 *
 * The sketch runs in an iframe of its own, built from a srcdoc: p5 in global
 * mode declares setup(), draw() and every variable on window, and two sketches
 * on one page would overwrite each other. Not sandboxed — the code is the
 * course's own, and a same-origin frame is what lets loadImage() reach
 * /documents/ and this script read the canvas height.
 *
 * It only runs while it is shown. Switching back to the code removes the
 * frame, which stops draw() rather than leaving it burning sixty times a
 * second behind a hidden panel; showing it again starts the sketch over.
 */

// p5's package exports do not list the built file, so it is reached by path.
// Bundled by Vite and served from this site: no CDN between a class and its
// sketches, and the version is the one package.json pins.
import p5Url from '../../node_modules/p5/lib/p5.min.js?url'

type View = 'code' | 'render'

// Where loadImage("billy.png") and the scripts= files are looked up.
const DOCUMENTS = '/documents/'

let count = 0

class P5Sketch extends HTMLElement {
	#tabs: HTMLButtonElement[] = []
	#panels = new Map<View, HTMLElement>()
	#frame: HTMLIFrameElement | null = null
	#observer: ResizeObserver | null = null

	connectedCallback(): void {
		if (this.#tabs.length > 0) {
			return
		}

		const tablist = this.querySelector<HTMLElement>('[data-switch]')

		if (tablist === null) {
			return
		}

		const id = `sketch-${++count}`

		this.#tabs = [...tablist.querySelectorAll<HTMLButtonElement>('[data-view]')]

		for (const tab of this.#tabs) {
			const view = tab.dataset.view as View
			const panel = this.querySelector<HTMLElement>(`[data-panel="${view}"]`)

			if (panel === null) {
				continue
			}

			this.#panels.set(view, panel)

			tab.id = `${id}-${view}-tab`
			panel.id = `${id}-${view}`
			tab.setAttribute('role', 'tab')
			tab.setAttribute('aria-controls', panel.id)
			panel.setAttribute('role', 'tabpanel')
			panel.setAttribute('aria-labelledby', tab.id)

			tab.addEventListener('click', () => this.show(view))
			tab.addEventListener('keydown', (event) => this.#move(event, tab))
		}

		tablist.setAttribute('role', 'tablist')
		tablist.setAttribute('aria-label', 'Affichage du sketch')
		tablist.hidden = false

		this.show('code')
	}

	disconnectedCallback(): void {
		this.#stop()
	}

	show(view: View): void {
		for (const tab of this.#tabs) {
			const selected = tab.dataset.view === view

			tab.setAttribute('aria-selected', String(selected))
			tab.tabIndex = selected ? 0 : -1
		}

		for (const [name, panel] of this.#panels) {
			panel.hidden = name !== view
		}

		if (view === 'render') {
			this.#run()
		} else {
			this.#stop()
		}
	}

	/** Arrow keys move between the tabs, as a tab list does everywhere else. */
	#move(event: KeyboardEvent, tab: HTMLButtonElement): void {
		const index = this.#tabs.indexOf(tab)
		const last = this.#tabs.length - 1

		const next = {
			ArrowLeft: index === 0 ? last : index - 1,
			ArrowRight: index === last ? 0 : index + 1,
			Home: 0,
			End: last,
		}[event.key]

		if (next === undefined) {
			return
		}

		event.preventDefault()

		const target = this.#tabs[next]

		target.focus()
		this.show(target.dataset.view as View)
	}

	#run(): void {
		const panel = this.#panels.get('render')

		if (panel === undefined || this.#frame !== null) {
			return
		}

		const frame = document.createElement('iframe')

		frame.className = 'c-sketch__canvas'
		frame.title = 'Rendu du sketch'
		frame.srcdoc = this.#document()
		frame.addEventListener('load', () => this.#fit(frame))

		this.#frame = frame
		panel.replaceChildren(frame)
	}

	#stop(): void {
		this.#observer?.disconnect()
		this.#observer = null
		this.#frame?.remove()
		this.#frame = null
	}

	/**
	 * Holds the frame to the height of what the sketch drew.
	 *
	 * The ratio sketch.css reserves is only a guess from createCanvas(); a
	 * sketch that sizes itself from the window, or adds a slider under its
	 * canvas, is taller or shorter than that. Nothing is set while the body is
	 * still empty — setup() may be awaiting an image — so the reserved box
	 * holds until there is something to measure.
	 */
	#fit(frame: HTMLIFrameElement): void {
		const body = frame.contentDocument?.body

		if (body == null) {
			return
		}

		this.#observer = new ResizeObserver(() => {
			const height = body.scrollHeight

			if (height > 0) {
				frame.style.blockSize = `${height}px`
			}
		})

		this.#observer.observe(body)
	}

	#document(): string {
		const code = this.querySelector('[data-panel="code"] pre')?.textContent ?? ''
		const base = new URL(DOCUMENTS, location.href).href
		const p5 = new URL(p5Url, import.meta.url).href
		const scripts = (this.dataset.scripts ?? '').split(/\s+/).filter(Boolean)

		// The canvas keeps the size the sketch gave it and only ever shrinks to
		// the frame. p5 writes its width and height inline, hence !important.
		return `<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8">
<base href="${base}">
<style>
html, body { margin: 0; }
canvas { display: block; max-inline-size: 100%; block-size: auto !important; }
</style>
<script src="${p5}"></script>
${scripts.map((src) => `<script src="${src}"></script>`).join('\n')}
</head>
<body>
<script>
${code.replace(/<\/script/gi, '<\\/script')}
</script>
</body>
</html>`
	}
}

customElements.define('p5-sketch', P5Sketch)
