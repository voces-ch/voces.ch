import { computed } from "vue";

const uiDictionary = {
    en: {
        goal: "Goal",
        errorTitle: "Submission Failed",
    },
    de: {
        goal: "Ziel",
        errorTitle: "Übertragung fehlgeschlagen",
    },
    fr: {
        goal: "Objectif",
        errorTitle: "Échec de la soumission",
    },
    it: {
        goal: "Obiettivo",
        errorTitle: "Invio non riuscito",
    },
};

export function useTranslations(langProp) {
    const t = computed(
        () => uiDictionary[langProp.value] || uiDictionary["en"],
    );
    return { t };
}
