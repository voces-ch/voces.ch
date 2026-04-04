import { createApp } from 'vue'
import App from './App.vue'

// 1. Create the initialization function
const initWidget = (config = {}) => {
    // 2. Validate the target exists
    const target = config.target || '#voces-widget'
    const el = document.querySelector(target)

    if (!el) {
        console.error(`Voces Error: Target element "${target}" not found.`);
        return;
    }

    // 3. Initialize the Vue app with the config passed as props
    const app = createApp(App, {
        campaignUuid: config.campaignUuid,
        source: config.source || 'organic'
    })

    // 4. Mount it!
    app.mount(el)
}

// 5. Expose it to the global window object
window.voces = {
    widget: initWidget
}
