<template>
  <div>
    <MasterAdminLayout>
      <v-dialog v-model="licenceIssuedDialog" width="500">
        <v-card>
          <h3 class="text-h5 text-center pt-3">Are you sure?</h3>
          <v-card-text class="text-center">
            <p>
              This confirms that the licence is issued to the trainee. Are you
              want to proceed?
            </p>
          </v-card-text>
          <div class="d-flex justify-content-around px-2 py-3">
            <v-btn class="text-capitalize" @click="licenceIssuedDialog = false">
              No
            </v-btn>
            <v-btn
              class="text-white text-capitalize"
              color="green accent-5"
              @click="licenceIssued(id)"
            >
              Yes
            </v-btn>
          </div>
        </v-card>
      </v-dialog>

      <v-card class="border">
        <v-card-text>
          <v-row>
            <v-col>
              <div class="page-header">
                <p class="page-heading">BDE Course</p>
                <Dropdown
                  v-model="selectedOption"
                  :options="bdeOptions"
                  optionLabel="option"
                  optionValue="value"
                  @input="bdeList(1)"
                  placeholder="Filter Course"
                  style="width: 15rem; border-radius: 5px"
                />
              </div>

              <DataTable :value="data">
                <Column sortable header="">
                  <template #body="slotProps">
                    <img
                      :src="slotProps.data.user.avatar"
                      alt="avatar"
                      class="v-avatar"
                      style="width: 60px; height: 60px"
                    />
                  </template>
                </Column>
                <Column sortable field="user.fullname" header="Name">
                  <template #body="slotProps">
                    <router-link
                      :to="
                        '/admin/trainees-details/' + slotProps.data.trainee_id
                      "
                    >
                      {{ slotProps.data.user.fullname }}
                    </router-link>
                  </template>
                </Column>
                <Column sortable field="" header="Status">
                  <template #body="slotProps">
                    <div class="text-center">
                      <v-chip
                        class="completed text-white"
                        small
                        v-if="
                          slotProps.data.total_hours ===
                            slotProps.data.completed_hours &&
                          slotProps.data.licence_issued === 0
                        "
                      >
                        Completed
                      </v-chip>
                      <v-chip
                        class="secondary text-white"
                        small
                        v-else-if="slotProps.data.licence_issued !== 0"
                      >
                        Licence Issued
                      </v-chip>
                      <v-chip
                        class="reserved text-white"
                        small
                        v-else-if="
                          slotProps.data.total_hours ===
                            slotProps.data.remaining_hours &&
                          slotProps.data.licence_issued === 0
                        "
                      >
                        Initiated
                      </v-chip>
                      <v-chip
                        class="reserved text-white"
                        small
                        v-else-if="
                          slotProps.data.total_hours >
                            slotProps.data.completed_hours &&
                          slotProps.data.licence_issued === 0
                        "
                      >
                        In Progress
                      </v-chip>
                    </div>
                  </template>
                </Column>
                <Column
                  sortable
                  field="total_hours"
                  header="Total Hours"
                ></Column>
                <Column
                  sortable
                  field="completed_hours"
                  header="Completed Hours"
                ></Column>
                <Column
                  sortable
                  field="remaining_hours"
                  header="Remaining Hours"
                ></Column>
                <Column sortable field="is_pass" header="Result">
                  <template #body="slotProps">
                    <div class="text-center">
                      <v-chip
                        class="completed text-white"
                        small
                        v-if="slotProps.data.is_pass === 'Pass'"
                      >
                        Pass
                      </v-chip>
                      <v-chip
                        class="red text-white"
                        small
                        v-else-if="slotProps.data.is_pass === 'Fail'"
                      >
                        Fail
                      </v-chip>
                      <span v-else>{{ slotProps.data.is_pass }}</span>
                    </div>
                  </template>
                </Column>
                <Column header="Details">
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
                        <v-list-item
                          :to="{
                            name: 'bde-log',
                            params: { username: slotProps.data.user.username },
                          }"
                        >
                          Details
                        </v-list-item>
                        <v-list-item
                          v-if="
                            slotProps.data.licence_issued === 0 &&
                            slotProps.data.is_pass === 'Pass'
                          "
                          @click="dialogMethod(slotProps.data.trainee_id)"
                        >
                          Licence Issued
                        </v-list-item>
                      </v-list>
                    </v-menu>
                  </template>
                </Column>
              </DataTable>
              <v-pagination
                  v-model="page"
                  @input="handlePageChange"
                  :length="count"
                  :total-visible="7"
                  pills
              ></v-pagination>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>
    </MasterAdminLayout>
  </div>
</template>

<script>
import MasterAdminLayout from "../Layouts/MasterAdminLayout";
import BasicDatatable from "../../../components/Global/BasicDatatable.vue";
export default {
  name: "BdeCourse",
  components: {
    MasterAdminLayout,
    BasicDatatable,
  },
  data() {
    return {
      data:[],
      page: 1,
      count: 1,
      pageSize: 1,
      perPage: 10,
      search:'',
       selectedOption: null,
      bdeOptions: [
        { option: "In Progress", value: 1 },
        { option: "Completed", value: 2 },
        { option: "Licence Issued", value: 3 },
      ],
      licenceIssuedDialog: false,
    };
  },
  computed: {
    filteredBdeList() {
      let allBde = this.data;

      if (this.selectedOption == null) return allBde;

      if (this.selectedOption === 1) {
        let storageObject = {
          option: "In Progress",
          value: this.selectedOption,
        };
        sessionStorage.filteredBdeValue = JSON.stringify(storageObject);
        return allBde.filter(
          (bde) =>
            bde.total_hours > bde.completed_hours && bde.licence_issued === 0
        );
      }

      if (this.selectedOption === 2) {
        let storageObject = {
          option: "Completed",
          value: this.selectedOption,
        };
        sessionStorage.filteredBdeValue = JSON.stringify(storageObject);
        return allBde.filter(
          (bde) =>
            bde.total_hours === bde.completed_hours && bde.licence_issued === 0
        );
      }

      if (this.selectedOption === 3) {
        let storageObject = {
          option: "Licence Issued",
          value: this.selectedOption,
        };
        sessionStorage.filteredBdeValue = JSON.stringify(storageObject);
        return allBde.filter(
          (bde) =>
            bde.total_hours === bde.completed_hours && bde.licence_issued !== 0
        );
      }

      return allBde;
    },
  },
  mounted() {
    let sessionObject = sessionStorage.filteredBdeValue;
    if (sessionObject) {
      let obj = JSON.parse(sessionObject);
      this.bdeOptions.option = obj.option;
      this.selectedOption = obj.value;
    }

    window.onunload = function () {
      sessionStorage.removeItem("filteredBdeValue");
    };

    this.bdeList();
  },
  methods: {
    handlePageChange(value) {
      this.page = value;
      this.bdeList();
    },
    dialogMethod(id) {
      this.id = id;
      this.licenceIssuedDialog = true;
    },
    async bdeList(page=this.page) {

      this.page=page;
      const { data } = await axios.get("/v1/drivisa/admin/bde/list?per_page=15&&page=" + this.page+"&&type=" + this.selectedOption);
      this.data = data.data;
      let total = data.total / 15;
      this.count = total % 1 === 0 ? parseInt(total) : parseInt(total + 1);
    },
    async licenceIssued(id) {
      try {
        const url = `/v1/drivisa/admin/bde/${id}/licence-issued`;
        const { data } = await axios.post(url);
        this.$toasted.success(data.message);
        this.licenceIssuedDialog = false;
        this.bdeList();
      } catch (e) {}
    },
  },
};
</script>
