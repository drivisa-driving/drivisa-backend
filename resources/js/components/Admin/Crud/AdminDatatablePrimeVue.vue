<template>
  <div>
    <BasicDatatable :data="data">
      <Column sortable field="fullName" header="Name"></Column>
      <Column sortable field="email" header="Email"></Column>
      <Column sortable field="lastLogin" header="Last Login"></Column>
      <Column sortable field="createdAt" header="Join On"></Column>
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
              <v-list-item
                v-if="slotProps.data.email != admin.email"
                @click="$emit('delete', slotProps.data.id)"
              >
                Delete
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
  name: "AdminDatatable",
  components: {
    BasicDatatable,
  },
  data() {
    return {
      data: [],
    };
  },
  mounted() {
    this.getAllAdmin();
  },
  computed: {
    admin() {
      return this.$store.state.user.user;
    },
  },
  methods: {
    async getAllAdmin() {
      try {
        const { data } = await axios.get("/v1/drivisa/admin/admins");
        this.data = data;
        this.data = data.data;
      } catch (e) {}
    },
    onEdit(admin) {
      this.$emit("edit", admin);
    },
  },
};
</script>
