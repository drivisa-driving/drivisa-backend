<template>
  <div>
    <DataTableApi endpoint="/v1/auth/login-history">
      <Column sortable field="browser" header="Browser"></Column>
      <Column
        sortable
        field="operatingSystem"
        header="Operating System"
      ></Column>
      <Column sortable field="lastLogin" header="Last Login"></Column>
    </DataTableApi>
  </div>
</template>

<script>
import DataTableApi from "../../../components/Global/DatatableApi.vue";
export default {
  name: "LoginHistory",
  components: { DataTableApi },
};
</script>
