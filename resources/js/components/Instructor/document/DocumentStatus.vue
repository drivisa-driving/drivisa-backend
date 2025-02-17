<template>
  <p><strong>Verification Status:</strong>
    <span
        v-if="document.status == 1"
        class="text-primary"
    >In review</span>
    <span
        v-else-if="document.status == 2"
        class="text-success"
    >Approved</span>
    <span
        v-else-if="document.status == 3"
        class="text-danger"
    >Rejected due to {{ document.reason }}</span>
  </p>
</template>

<script>
export default {
  name: "DocumentStatus",
  props: ['document'],
}
</script>

<style scoped>

</style>