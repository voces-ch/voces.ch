import { createApp } from "vue";
import App from "./App.vue";
import "./assets/css/app.css";

const loadAltchaScript = () => {
    if (!document.querySelector('script[src*="altcha.min.js"]')) {
        const script = document.createElement("script");
        script.src =
            "https://cdn.jsdelivr.net/gh/altcha-org/altcha/dist/altcha.min.js";
        script.async = true;
        script.defer = true;
        script.type = "module";
        document.head.appendChild(script);
    }
};

const initWidget = (config = {}) => {
    const target = config.target || "#voces-widget";
    const el = document.querySelector(target);

    if (!el) {
        console.error(`Voces Error: Target element "${target}" not found.`);
        return;
    }
    loadAltchaScript();

    const urlParams = new URLSearchParams(window.location.search);
    const finalSource = urlParams.get("source") || config.source || null;
    const finalOrigin =
        urlParams.get("origin") || config.origin || window.location.hostname;

    const apiUrl =
        import.meta.env.VITE_API_URL || "https://app.voces.lndo.site/api/v1";
    const app = createApp(App, {
        campaignUuid: config.campaignUuid,
        source: finalSource,
        origin: finalOrigin,
        lang: config.lang || "de",
        theme: config.theme || "minimal",
        showProgress: config.showProgress || false,
        apiUrl: apiUrl,
    });

    app.mount(el);
};

window.voces = {
    widget: initWidget,
};
