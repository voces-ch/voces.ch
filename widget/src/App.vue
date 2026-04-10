<script setup>
import { onMounted, toRefs, ref } from "vue";
import { useTranslations } from "./composables/useTranslations";
import { useCampaign } from "./composables/useCampaign";
import ProgressBar from "./components/ProgressBar.vue";
import { marked } from "marked";

const props = defineProps({
    campaignUuid: { type: String, required: true },
    source: { type: String, default: null },
    origin: { type: String, default: null },
    lang: { type: String, default: "de" },
    theme: { type: String, default: "minimal" },
    showProgress: { type: Boolean, default: false },
    apiUrl: { type: String, required: true },
});

const { lang } = toRefs(props);
const { t } = useTranslations(lang);

const {
    campaignData,
    formData,
    successMessage,
    hideForm,
    validationErrors,
    serverError,
    isSubmitting,
    fetchCampaign,
    submitForm,
} = useCampaign(props);

const progressBarRef = ref(null);

const isCaptchaSolved = ref(false);

onMounted(async () => {
    await fetchCampaign();
});

const handleAltchaChange = (event) => {
    if (event.detail.state === "verified") {
        formData.value.altcha = event.detail.payload;
        isCaptchaSolved.value = true;
    } else {
        formData.value.altcha = null;
        isCaptchaSolved.value = false;
    }
};

const handleSubmit = async () => {
    const oldCount = campaignData.value.signature_count;
    await submitForm(() => {
        campaignData.value.signature_count++;
        if (progressBarRef.value) {
            progressBarRef.value.updateProgress(
                oldCount,
                campaignData.value.signature_count,
            );
        }
    });
};
</script>

<template>
    <div class="voces-widget" :class="`voces-theme-${props.theme}`">
        <ProgressBar
            v-if="props.showProgress && campaignData?.signature_goal"
            ref="progressBarRef"
            :goal="campaignData.signature_goal"
            :count="campaignData.signature_count"
            :t="t"
        />

        <div v-if="serverError" class="voces-alert-error">
            <div class="voces-alert-icon-wrapper">
                <svg
                    class="voces-alert-icon"
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 20 20"
                    fill="currentColor"
                    aria-hidden="true"
                >
                    <path
                        fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
                        clip-rule="evenodd"
                    />
                </svg>
            </div>
            <div class="voces-alert-content">
                <h3 class="voces-alert-title">{{ t.errorTitle }}</h3>
                <div class="voces-alert-text">
                    <p>{{ serverError }}</p>
                </div>
            </div>
        </div>

        <form
            v-if="!hideForm"
            class="voces-form"
            @submit.prevent="handleSubmit"
        >
            <div
                v-for="field in campaignData?.fields"
                :key="field.name"
                class="voces-field"
            >
                <label
                    v-if="field.type !== 'checkbox'"
                    class="voces-label"
                    v-html="
                        marked.parse(field.label) +
                        (field.is_required
                            ? ' <span class=&quot;voces-required&quot;>*</span>'
                            : '')
                    "
                >
                </label>

                <textarea
                    v-if="field.type === 'textarea'"
                    v-model="formData[field.name]"
                    :required="field.is_required"
                    class="voces-textarea"
                ></textarea>

                <label
                    v-else-if="field.type === 'checkbox'"
                    class="voces-checkbox-label"
                >
                    <input
                        type="checkbox"
                        v-model="formData[field.name]"
                        :required="field.is_required"
                        class="voces-checkbox"
                    />
                    <span
                        class="voces-checkbox-text"
                        v-html="marked.parse(field.label)"
                    ></span>
                    <span v-if="field.is_required" class="voces-required"
                        >*</span
                    >
                </label>

                <input
                    v-else
                    :type="field.type"
                    v-model="formData[field.name]"
                    :required="field.is_required"
                    class="voces-input"
                />

                <p
                    v-if="validationErrors[field.name]"
                    class="voces-validation-error"
                >
                    {{ validationErrors[field.name] }}
                </p>
            </div>

            <div class="voces-field voces-altcha-wrapper">
                <div class="w-fit rounded-md bg-white p-2 shadow-sm">
                    <altcha-widget
                        :challengeurl="`${props.apiUrl}/auth/challenge`"
                        :hidefooter="true"
                        @statechange="handleAltchaChange"
                    ></altcha-widget>
                </div>
                <p
                    v-if="validationErrors.altcha"
                    class="voces-validation-error"
                >
                    {{ validationErrors.altcha }}
                </p>
            </div>

            <button type="submit" class="voces-button" :disabled="isSubmitting">
                {{ campaignData?.submit_label || "Submit" }}
            </button>
        </form>

        <div v-else class="voces-alert-success" role="alert">
            <div v-html="successMessage" class="voces-response-message"></div>
        </div>
    </div>
</template>
