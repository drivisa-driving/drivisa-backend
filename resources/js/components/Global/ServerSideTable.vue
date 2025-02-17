<template>
  <DataTable
      ref="dt"
      :value="data"
      dataKey="id"
      :rowHover="true"
      :loading="loading"
      stripedRows
      :rows="10"
      responsiveLayout="scroll"
  >

    <template #empty>
      <div>
        <strong> Data Not Available</strong>
      </div>
    </template>
    <slot></slot>
  </DataTable>
</template>

<script>
export default {
  name: "BasicDatatable",
  props: ["data", "heading"],
  data() {
    return {
      totalItems: 0,
      search: "",
      headers: [],
      loading: false,
    };
  },
  mounted() {
    this.loading = true;
    setTimeout(() => {
      this.loading = false;
    }, Math.random() * 2000 + 250);
  },
  methods: {
  },
};
</script>