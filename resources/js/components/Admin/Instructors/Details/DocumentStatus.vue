<template>
  <p>
    <strong>Verification Status:</strong>
    <span v-if="document.status == 1" class="text-primary">In review</span>
    <span v-else-if="document.status == 2" class="text-success">Approved</span>
    <span v-else-if="document.status == 3" class="text-danger"
      >Rejected, <br />
      <span style="color: black">Reason:</span> {{ document.reason }}</span
    >
    <br /><span>Date: {{ document.updated_at }}</span>
  </p>
</template>
  
  <script>
export default {
  name: "DocumentStatus",
  props: ["document"],
};
</script>