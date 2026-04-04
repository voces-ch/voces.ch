import { ref } from "vue";

export function useCampaign(props) {
    const campaignData = ref(null);
    const formData = ref({});
    const successMessage = ref("");
    const hideForm = ref(false);
    const validationErrors = ref({});
    const serverError = ref(null);
    const isSubmitting = ref(false);

    const fetchCampaign = async () => {
        const baseUrl = import.meta.env.VITE_API_URL;
        const response = await fetch(
            `${baseUrl}/campaigns/${props.campaignUuid}?locale=${props.lang}`,
        );
        const json = await response.json();
        campaignData.value = json.data;

        const initialData = {};
        campaignData.value.fields.forEach((field) => {
            let val = field.default_value;
            if (field.type === "checkbox") {
                val =
                    val === "1" ||
                    val === "true" ||
                    val === "on" ||
                    val === true;
            } else {
                val = val || "";
            }
            initialData[field.name] = val;
        });
        formData.value = initialData;
    };

    const submitForm = async (onSuccessCallback) => {
        validationErrors.value = {};
        serverError.value = null;
        isSubmitting.value = true;

        const payload = {
            source: props.source,
            origin: props.origin,
            payload: formData.value,
        };

        try {
            const baseUrl = import.meta.env.VITE_API_URL;
            const response = await fetch(
                `${baseUrl}/campaigns/${props.campaignUuid}/signatures?language=${props.lang}`,
                {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                    },
                    body: JSON.stringify(payload),
                },
            );

            if (response.status === 422) {
                const errorData = await response.json();
                const formattedErrors = {};
                for (const [key, messages] of Object.entries(
                    errorData.errors,
                )) {
                    formattedErrors[key.replace("payload.", "")] = messages[0];
                }
                validationErrors.value = formattedErrors;
                return;
            }

            if (!response.ok) {
                let errorMessage = `Server returned a ${response.status} error.`;
                try {
                    const errorData = await response.json();
                    if (errorData.message) errorMessage = errorData.message;
                } catch (e) {}
                throw new Error(errorMessage);
            }

            if (onSuccessCallback) onSuccessCallback();

            switch (campaignData.value.success_type) {
                case "message":
                    successMessage.value =
                        campaignData.value.success_message ||
                        "Thank you for signing!";
                    hideForm.value = true;
                    break;
                case "redirect":
                    window.location.href = campaignData.value.success_url;
                    break;
            }
        } catch (error) {
            console.error(error);
            serverError.value =
                error.message ||
                "An unexpected error occurred. Please try again later.";
        } finally {
            isSubmitting.value = false;
        }
    };

    return {
        campaignData,
        formData,
        successMessage,
        hideForm,
        validationErrors,
        serverError,
        isSubmitting,
        fetchCampaign,
        submitForm,
    };
}
