<template>
  <div>
    <v-dialog v-model="moreDetailsDialog" width="500">
      <v-card>
        <h3 class="text-h5 text-center py-3">Package Details</h3>
        <div class="m-3">
          <table class="table table-bordered">
            <tr>
              <td>Hours</td>
              <td>{{ data["hours"] }}</td>
            </tr>
            <tr>
              <td>Hour Charge</td>
              <td>{{ data["hour_charge"] }}</td>
            </tr>
            <tr>
              <td>Amount</td>
              <td>{{ data["amount"] }}</td>
            </tr>
            <tr>
              <td>Instructor</td>
              <td>{{ data["instructor"] }}</td>
            </tr>
            <tr>
              <td>Drivisa</td>
              <td>{{ data["drivisa"] }}</td>
            </tr>
            <tr>
              <td>PDIO</td>
              <td>{{ data["pdio"] }}</td>
            </tr>
            <tr>
              <td>MTO</td>
              <td>{{ data["mto"] }}</td>
            </tr>
            <tr>
              <td>Instructor Cancel Fee</td>
              <td>{{ data["instructor_cancel_fee"] }}</td>
            </tr>
            <tr>
              <td>PDIO Cancel Fee</td>
              <td>{{ data["pdio_cancel_fee"] }}</td>
            </tr>
            <tr>
              <td>MTO Cancel Fee</td>
              <td>{{ data["mto_cancel_fee"] }}</td>
            </tr>
            <tr>
              <td>Drivisa Cancel Fee</td>
              <td>{{ data["drivisa_cancel_fee"] }}</td>
            </tr>
            <tr>
              <td>Discount Price</td>
              <td>{{ data["discount_price"] }}</td>
            </tr>
            <tr>
              <td>Tax</td>
              <td>{{ data["tax"] }}</td>
            </tr>
          </table>
        </div>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn @click="moreDetailsDialog = false"> Close </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <BasicDatatable :data="data">
      <Column sortable field="name" header="Name"></Column>
      <Column sortable field="packageType.name" header="Package Type"></Column>
      <Column
        sortable
        field="packageData.additional_information"
        header="More Info"
      ></Column>
      <Column header="Actions">
        <template #body="slotProps">
          <v-menu left>
            <template v-slot:activator="{ on, attrs }">
              <span
                class="mdi mdi-dots-vertical option-icon text-warning text-large float-left option-icon"
                v-bind="attrs"
                v-on="on"
              ></span>
            </template>
            <v-list>
              <v-list-item @click="onEdit(slotProps.data)"> Edit </v-list-item>
              <v-list-item @click="$emit('delete', slotProps.data.id)">
                Delete
              </v-list-item>
              <v-list-item @click="moreDetails(slotProps.data)"
                >Details
              </v-list-item>
            </v-list>
          </v-menu>
        </template>
      </Column>
    </BasicDatatable>
  </div>
</template>

<script>
import BasicDatatable from "../../Global/BasicDatatable.vue";
export default {
  name: "PackageDataTable",
  components: { BasicDatatable },
  data() {
    return {
      data: [],
      moreDetailsDialog: false,
    };
  },
  mounted() {
    this.getAllPackage();
  },
  methods: {
    filterOnlyCapsText(value, search, item) {
      return (
        value != null &&
        search != null &&
        typeof value === "string" &&
        value.toString().toLocaleUpperCase().indexOf(search) !== -1
      );
    },
    moreDetails(data) {
      this.data = { ...data["packageData"] };
      this.moreDetailsDialog = true;
    },
    async getAllPackage() {
      try {
        const { data } = await axios.get("/v1/drivisa/admin/packages");
        this.data = data;
        this.data = data.data;
      } catch (e) {}
    },
    onEdit(singlePackage) {
      this.$emit("edit", singlePackage);
    },
  },
};
</script>
