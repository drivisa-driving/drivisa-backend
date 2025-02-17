<template>
  <div>
    <BasicDatatable :data="reviews">
      <Column sortable field="profilePhoto" header="">
        <template #body="slotProps">
          <img
            :src="slotProps.data.profilePhoto"
            alt="avatar"
            class="v-avatar"
            style="width: 80px; height: 80px"
          />
        </template>
      </Column>
      <Column sortable field="name" header="Trainee">
        <template #body="slotProps">
          <router-link
            :to="{
              name: 'admin-trainees-details',
              params: { id: slotProps.data.trainee_id },
            }"
          >
            {{ slotProps.data.name }}
          </router-link>
        </template>
      </Column>
      <Column sortable field="note" header="Note"></Column>
      <Column sortable field="rating" header="Rate">
        <template #body="slotProps">
          <v-rating
            v-model="slotProps.data.rating"
            readonly
            background-color="orange darken"
            color="orange"
            icon-label="custom icon label text"
          ></v-rating>
        </template>
      </Column>
      <Column sortable field="formattedDate" header="Date"></Column>
    </BasicDatatable>
  </div>
</template>

<script>
import BasicDatatable from "../../../../components/Global/BasicDatatable.vue";
export default {
  name: "Reviews",
  components: { BasicDatatable },
  props: ["reviews"],
};
</script>