<script setup>
import { onMounted, ref } from 'vue'

// Define props that come from the window.voces.widget() call
const props = defineProps({
    campaignUuid: {
        type: String,
        required: true
    },
    source: {
        type: String,
        default: 'organic'
    }
})

const campaignData = ref(null)

onMounted(async () => {
    // Now you use the prop to hit your new v1 API!
    const response = await fetch(`https://voces.lndo.site/api/v1/campaigns/${props.campaignUuid}`)
    const json = await response.json()
    console.log('Fetched campaign data:', json)
    campaignData.value = json.data
})
</script>

<template>
    <div v-if="campaignData" class="voces-wrapper">
        <h1>{{ campaignData.title }}</h1>
    </div>
    <div v-else>Loading petition...</div>
</template>
