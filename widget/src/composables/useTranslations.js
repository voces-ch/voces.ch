import { computed } from "vue";

const uiDictionary = {
    en: {
        signatures: "signatures",
        goal: "Goal",
        errorTitle: "Submission Failed",
    },
    de: {
        signatures: "Unterschriften",
        goal: "Ziel",
        errorTitle: "Übertragung fehlgeschlagen",
    },
    fr: {
        signatures: "signatures",
        goal: "Objectif",
        errorTitle: "Échec de la soumission",
    },
    it: {
        signatures: "firme",
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
