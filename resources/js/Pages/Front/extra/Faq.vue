<template>
  <div>
    <Header/>
    <v-container class="d-container">
      <v-row>
        <v-col>
          <div class="faq">
            <h1 class="font-weight-bold text-center">FAQ</h1>
          </div>

          <v-expansion-panels
              flat
              hover
              v-model="expand"
              multiple
          >
            <v-expansion-panel
                v-for="(panel,i) in panels"
                :key="i"
                class="panel"
            >
              <v-expansion-panel-header
                  @click="panel.isOpened = !panel.isOpened"

              >
                <h5>{{ panel.title }}</h5>
                <template v-slot:actions>
                  <v-icon v-if="panel.isOpened">
                    mdi-minus
                  </v-icon>
                  <v-icon v-else>
                    mdi-plus
                  </v-icon>
                </template>
              </v-expansion-panel-header>
              <v-expansion-panel-content>
                {{ panel.text }}
              </v-expansion-panel-content>
            </v-expansion-panel>
          </v-expansion-panels>

        </v-col>
      </v-row>
    </v-container>
    <Footer/>
  </div>
</template>

<script>
import Header from "../../../components/Front/Header";
import Footer from "../../../components/Front/Footer";
import panelsData from "../../../data/faq";

export default {
  name: "Faq",
  components: {
    Header,
    Footer
  },
  data() {
    return {
      expand: [],
      panels: panelsData
    }
  }
}
</script>

<style lang="scss">
@import "resources/js/scss/vue_variables";

.panel {
  background: $header-bg;
  box-shadow: none;
  margin: 1rem 0;
}

.v-expansion-panel-header::before, .v-expansion-panel::before {
  content: none;
}


.v-expansion-panel-header:hover {
  background: rgba(0,0,0,.04) !important;
}
.v-expansion-panel-header {
  background: rgba(232,241,255,.3)!important; //#f1f6fd
  display: flex;
  flex-direction: row;
  align-items: center;
  padding: 0 24px;
  border-radius: inherit;
  transition: height 225ms cubic-bezier(0.4, 0, 0.2, 1);

  h5 {
    font-size: 20px;
    font-weight: 200 !important;
    color: #1c252c;
  }

}

.v-expansion-panel-content {
  background: #f5f8fc;
}

.faq {
  margin-top: 4rem;
  margin-bottom: 0.5rem;


  h1 {
    text-align: center;
    font-family: Montserrat, serif;
    font-style: normal;
    font-weight: 600;
    font-size: 35px;
    line-height: 60px;
  }
}
@media (max-width: 600px) {
  .faq {
    margin-top: 0!important;
  }
}
</style>