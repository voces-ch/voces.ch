<script setup>
import { ref, computed, watch } from "vue";

const props = defineProps({
    goal: { type: Number, required: true },
    count: { type: Number, required: true },
    t: { type: Object, required: true },
});

const progressSection = ref(null);
const hasAnimated = ref(false);
const displayedCount = ref(0);
const displayedPercentage = ref(0);

const targetPercentage = computed(() => {
    if (!props.goal || !props.count) return 0;
    return Math.min(Math.round((props.count / props.goal) * 100), 100);
});

const animateNumbers = (start, end) => {
    let startTimestamp = null;
    const duration = 1000;
    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        const easeOut = 1 - Math.pow(1 - progress, 3);
        displayedCount.value = Math.floor(easeOut * (end - start) + start);
        if (progress < 1) window.requestAnimationFrame(step);
        else displayedCount.value = end;
    };
    window.requestAnimationFrame(step);
};

const updateProgress = (oldCount, newCount) => {
    if (hasAnimated.value) {
        displayedPercentage.value = targetPercentage.value;
        animateNumbers(oldCount, newCount);
    }
};
defineExpose({ updateProgress });

watch(progressSection, (el) => {
    if (!el) return;
    const observer = new IntersectionObserver(
        (entries) => {
            if (entries[0].isIntersecting && !hasAnimated.value) {
                hasAnimated.value = true;
                displayedPercentage.value = targetPercentage.value;
                animateNumbers(0, props.count);
                observer.disconnect();
            }
        },
        { threshold: 0.2 },
    );
    observer.observe(el);
});
</script>

<template>
    <div ref="progressSection" class="voces-progress-wrapper">
        <div class="voces-progress-stats">
            <span
                ><strong>{{ displayedCount }}</strong> {{ t.signatures }}</span
            >
            <span>{{ t.goal }}: {{ goal }}</span>
        </div>
        <div class="voces-progress-track">
            <div
                class="voces-progress-fill"
                :style="{ width: `${displayedPercentage}%` }"
            ></div>
        </div>
    </div>
</template>
