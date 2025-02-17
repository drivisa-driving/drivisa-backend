<template>
  <div>
    <BasicDatatable :data="data">
      <Column sortable field="name" header="Name"></Column>
      <Column
        sortable
        field="packages_count"
        header="Packages Available"
      ></Column>
      <Column header="Actions">
        <template #body="slotProps">
          <v-btn
            color="warning"
            small
            class="text-capitalize"
            @click="onEdit(slotProps.data)"
            >Edit
          </v-btn>
          <v-btn
            color="red"
            small
            class="text-capitalize text-white"
            @click="$emit('delete', slotProps.data.id)"
            >Delete
          </v-btn>
        </template>
      </Column>
    </BasicDatatable>
  </div>
</template>

<script>
import BasicDatatable from "../../Global/BasicDatatable.vue";
export default {
  name: "PackageTypeDataTable",
  components: { BasicDatatable },
  data() {
    return {
      data: [],
    };
  },
  mounted() {
    this.getAllPackageType();
  },
  methods: {
    async getAllPackageType() {
      try {
        const { data } = await axios.get(
          "/v1/drivisa/admin/with-packages-count"
        );
        this.data = data;
        this.data = data.data;
      } catch (e) {}
    },
    onEdit(packageType) {
      this.$emit("edit", packageType);
    },
  },
};
</script>