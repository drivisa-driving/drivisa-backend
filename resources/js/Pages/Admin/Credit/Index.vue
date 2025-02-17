<template>
  <div>
    <MasterAdminLayout>
      <v-card elevation="0" class="profile_section border">
        <v-card-text>
          <div class="page-header pl-0">
            <p class="page-heading">Credit</p>
          </div>
          <hr />
          <TabView ref="tabview1">
            <TabPanel header="Add">
              <AddCredit :trainees="trainees" />
            </TabPanel>
            <TabPanel header="Reduce">
              <ReduceCredit :trainees="trainees" />
            </TabPanel>
            <TabPanel header="Bonus/Reward">
              <BonusCredit :trainees="trainees" />
            </TabPanel>
          </TabView>
        </v-card-text>
      </v-card>
    </MasterAdminLayout>
  </div>
</template>

<script>
import MasterAdminLayout from "../Layouts/MasterAdminLayout";
import AddCredit from "./AddCredit.vue";
import ReduceCredit from "./ReduceCredit.vue";
import BonusCredit from "./BonusCredit.vue";

export default {
  name: "Credit",
  components: { MasterAdminLayout, AddCredit, ReduceCredit, BonusCredit },
  data() {
    return {
      trainees: [],
    };
  },
  mounted() {
    this.getTrainees();
  },
  methods: {
    async getTrainees() {
      try {
        const url = "/v1/drivisa/admin/trainees/all";
        const { data } = await axios.get(url);
        this.trainees = data;
      } catch (e) {}
    },
  },
};
</script>
