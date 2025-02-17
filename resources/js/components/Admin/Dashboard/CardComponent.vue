<template>
  <v-container fluid>
    <v-data-iterator :items="items" hide-default-footer>
      <template v-slot:default="props">
        <v-row>
          <v-col
            v-for="item in props.items"
            :key="item.name"
            cols="12"
            sm="6"
            md="6"
            lg="6"
          >
            <div class="card">
              <v-card-title
                class="font-weight-bold pb-0"
                style="font-size: 18px"
              >
                {{ item.name }}
              </v-card-title>
              <v-divider></v-divider>

              <v-list dense v-if="item.data.length > 0">
                <v-list-item v-for="data in item.data" :key="data.id">
                  <v-list-item-content>
                    <router-link :to="item.base_url + data.id">
                      {{ data.first_name + " " + data.last_name }}
                    </router-link>
                  </v-list-item-content>
                  <v-list-item-content>{{
                    data.verified ? "Verified" : "Not Verified"
                  }}</v-list-item-content>
                  <v-list-item-content class="align-end">
                    {{ data.created_at | moment("MMMM DD, YYYY h:mm A") }}
                  </v-list-item-content>
                </v-list-item>
              </v-list>
              <v-list v-else class="text-center"
                ><strong>No Data Available</strong>
              </v-list>
            </div>
          </v-col>
        </v-row>
      </template>
    </v-data-iterator>
  </v-container>
</template>

<script>
export default {
  name: "CardComponent",
  props: ["items"],
};
</script>
