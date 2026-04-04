import { createApp } from "vue";
import App from "./App.vue";
import "./assets/css/app.css";

const initWidget = (config = {}) => {
    const target = config.target || "#voces-widget";
    const el = document.querySelector(target);

    if (!el) {
        console.error(`Voces Error: Target element "${target}" not found.`);
        return;
    }

    const urlParams = new URLSearchParams(window.location.search);
    const finalSource = urlParams.get("source") || config.source || null;
    const finalOrigin =
        urlParams.get("origin") || config.origin || window.location.hostname;

    const app = createApp(App, {
        campaignUuid: config.campaignUuid,
        source: finalSource,
        origin: finalOrigin,
        lang: config.lang || "de",
        theme: config.theme || "minimal",
        showProgress: config.showProgress || false,
    });

    app.mount(el);
};

window.voces = {
    widget: initWidget,
};
