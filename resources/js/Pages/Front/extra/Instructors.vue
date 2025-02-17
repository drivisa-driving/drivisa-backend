<template>
  <div>
    <Header/>
    <section>
      <div class="d-container">
        <div class="filter_bar">
          <div class="d-form-field">
            <v-text-field
                label="Search"
                placeholder="search by name , address, post code, state"
                v-model="filter.term"
                v-on:keydown.enter="getInstructors(true, true)"
                outlined
            ></v-text-field>
          </div>
          <div class="d-form-field">
            <v-menu
                ref="menu1"
                v-model="menu1"
                :close-on-content-click="false"
                transition="scale-transition"
                offset-y
                max-width="290px"
                min-width="auto"
            >
              <template v-slot:activator="{ on, attrs }">
                <v-text-field
                    v-model="filter.date"
                    label="Date"
                    persistent-hint
                    outlined
                    prepend-inner-icon="mdi-calendar"
                    v-bind="attrs"
                    @blur="filter.date = parseDate(filter.date)"
                    v-on="on"
                ></v-text-field>
              </template>
              <v-date-picker
                  v-model="filter.date"
                  no-title
                  @input="menu1 = false"
              ></v-date-picker>
            </v-menu>
          </div>
          <div class="d-form-field">
            <v-autocomplete
                v-model="filter.make"
                :items="cars"
                outlined
                item-value="make"
                item-text="make"
                label="Car Maker"
            ></v-autocomplete>
          </div>
          <div class="d-form-field">
            <v-autocomplete
                v-model="filter.language"
                :items="langData"
                item-text="name"
                item-value="code"
                outlined
                label="Language"
            ></v-autocomplete>
          </div>
          <div class="filter_btn">
            <button @click="getInstructors(true, true)">
                <span>Filter
                  <i class="mdi mdi-magnify"></i>
                </span>
            </button>
          </div>
        </div>
        <div class="instructor-cards">
          <div class="row" v-if="instructors.length > 0">
            <div class="col-lg-4 col-sm-6" v-for="(inst, i) in instructors" :key="i">
              <router-link :to="{name:'single-instructor', params:{username:inst.username}}" style="color: black">
                <div class="d-card">
                  <div class="avatar">
                    <img class="img-fluid" :src="inst.avatar"
                         alt="">
                  </div>
                  <div class="content">
                    <div>
                      <p class="name">{{ inst.fullName }}</p>
                    </div>
                    <div class="address">
                      <i class="mdi mdi-map-marker mat_icon"></i>
                      <span v-if="inst.cars.length > 0" class="mat-tooltip-trigger">
                        {{ inst.point == null ? "Not Provided" : inst.point.sourceName }}
                     </span>
                      <span v-else>Not Provided yet</span>
                    </div>
                    <div class="car">
                      <i class="mdi mdi-car-back "></i>
                      <span v-if="inst.cars.length > 0" class="mat-tooltip-trigger">
                        {{ inst.cars[0].generation }}
                        {{ inst.cars[0].make }}
                        {{ inst.cars[0].model }}
                     </span>
                      <span v-else>Not Provided yet</span>
                    </div>
                    <div class="star_rating">
                      <v-rating
                          v-model="inst.evaluation.avg"
                          readonly
                          background-color="orange darken"
                          color="orange"
                          icon-label="custom icon label text"
                      ></v-rating>
                    </div>
                  </div>
                </div>
              </router-link>
            </div>
          </div>
          <div class="row" v-else>
            <div class="col-md-12">
              <h3 class="text-center text-dark">No Instructor Found</h3>
            </div>
          </div>
          <div class="d-paginator mt-7">
            <div class="d-paginator-outer-container">
              <div class="d-paginator-container">
                <div class="d-paginator-page-size">
                  <div class="d-paginator-page-size-label"> Items per page:</div>
                  <div class="d-paginator-page-size-value ng-star-inserted">10</div>
                </div>
                <div class="d-paginator-range-actions">
                  <div class="d-paginator-range-label"> {{ from }}-{{ to }} of {{ totalItem }}</div>
                  <button class="d-focus-indicator" v-on:click="getInstructors(false)">
                    <span class="d-button-wrapper">
                      <svg viewBox="0 0 24 24" class="d-paginator-icon"><path
                          d="M15.41 7.41L14 6l-6 6 6 6 1.41-1.41L10.83 12z"></path>
                      </svg>
                    </span>
                  </button>
                  <button class="d-focus-indicator" :disabled="to == totalItem" v-on:click="getInstructors">
                    <span class="d-button-wrapper">
                      <svg viewBox="0 0 24 24" focusable="false" class="d-paginator-icon">
                        <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"></path>
                      </svg>
                    </span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <Footer/>
  </div>
</template>

<script>
import Header from "../../../components/Front/Header";
import Footer from "../../../components/Front/Footer";
import langs from "../../../data/langData";

export default {
  name: "Instructors",
  components: {Footer, Header},
  data(vm) {
    return {
      // users: [],
      menu1: false,
      menu2: false,
      cars: [],
      values: [''],
      value: null,
      instructors: [],
      totalItem: 0,
      from: 1,
      to: 1,
      langData: langs,
      pageNo: 0,
      filter: {
        term: null,
        date: (new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10),
        dateFormatted: vm.formatDate((new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10)),
        make: null,
        language: null,
      }
    }
  },
  computed: {
    computedDateFormatted() {
      return this.formatDate(this.date)
    },
  },

  watch: {
    date(val) {
      this.dateFormatted = this.formatDate(this.date)
    },
  },

  mounted() {
    this.updateFilter()
    this.getInstructors();
    this.getCarMaker()
  },
  methods: {
    async getInstructors(isForward = true, isFilter = false) {
      const {data} = await this.$api.front.searchInstructors(this.pageNo, this.filter, isForward, isFilter);
      this.instructors = data.data
      this.pageNo = data.meta.current_page;
      this.totalItem = data.meta.total;
      this.from = data.meta.from;
      this.to = data.meta.to;
    },
    async getCarMaker() {
      const {data} = await this.$api.front.getCarMaker();
      this.cars = data.data
    },
    formatDate(date) {
      if (!date) return null

      const [year, month, day] = date.split('-')
      return `${month}/${day}/${year}`
    },
    parseDate(date) {
      if (!date) return null

      const [month, day, year] = date.split('/')
      return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`
    },
    updateFilter() {
      let filter = this.$route.query;

      this.filter = {
        term: filter.term,
        date: filter.date,
        make: filter.make,
        language: filter.language,
      }
    }
  },

}
</script>

<style scoped lang="scss">
section {
  margin-bottom: 45px;

  .filter_bar {
    display: grid;
    grid-template-columns: auto auto auto auto auto;
    grid-column-gap: 15px;
    text-align: center;
    padding: 5px;

    .d-form-field {
      flex: 1 1 100%;
      box-sizing: border-box;
      margin-right: 2rem;
      display: inline-block;
      position: relative;
      text-align: left;
      line-height: 1.125;
    }

    .filter_btn {
      display: flex;
      flex: 1 1 100%;
      place-content: center;
      align-items: center;
      min-height: 4rem;
      height: 4rem;

      button {
        background-color: #3f54d1;
        color: #fafafa;
        margin: 0;
        min-width: 64px;
        line-height: 36px;
        padding: 0 16px;
        border-radius: 4px;

        span {
          cursor: pointer;
          white-space: nowrap;
          text-align: center;
          color: #fafafa;
          font-size: 14px;
          font-weight: 500;

          i {
            font-size: 18px;
            font-weight: bold;
            margin-left: 5px;
          }
        }
      }
    }
  }

  @media (max-width: 960px) {
    .filter_bar {
      display: flex;
      flex-direction: column;

      .d-form-field {
        margin-bottom: 0.5rem;
        max-height: 100%;
      }
    }
  }

  .instructor-cards {
    .d-card {
      box-sizing: border-box;
      display: flex;
      flex: 1 1 100%;
      max-width: 100%;
      box-shadow: 0 20px 105.701px rgba(51, 51, 51, .1);
      padding: 16px 11px;
      border-radius: 20px;
      align-items: center;
      cursor: pointer;
      background-color: #fff;

      .avatar {
        flex: 1 1 100%;
        box-sizing: border-box;
        max-width: 20%;
        text-align: center;

        img {
          min-width: 80px;
          min-height: 80px;
          border-radius: 50%;
          object-fit: cover;
          width: 40px;
          height: 40px;
          margin: 0;
          line-height: 40px;
          font-size: 17px;
          font-weight: 600;
          text-align: center;
        }
      }

      .content {
        flex-direction: column;
        box-sizing: border-box;
        display: flex;
        overflow-x: hidden;
        margin-inline: 20px;

        .name {
          font-size: 16px;
          font-weight: 700;
          margin: 0;
          text-align: left;
        }

        .address {
          align-items: center;
          text-align: left;

          span {
            font-size: 12px;
            font-weight: 800;

          }

          i {
            font-size: 17px;
            margin: 6px 0 0;
            background-repeat: no-repeat;
            display: inline-block;
            fill: currentColor;
            font-weight: 400 !important;
            line-height: 1;
          }
        }

        .car {
          text-align: left;
          align-items: center;
          color: gray;

          span {
            margin-left: 2px;
            white-space: nowrap;
            color: gray;
          }

          i {
            background-repeat: no-repeat;
            display: inline-block;
            fill: currentColor;
            font-size: 25px;
            vertical-align: middle;
            font-weight: 400 !important;
            line-height: 1;
            color: gray;
          }
        }

        .star_rating {
          text-align: left !important;

          i {
            color: orange;
            vertical-align: middle;
            fill: currentColor;
            font-size: 24px;
            margin-right: 19px;
          }
        }
      }
    }

    @media (max-width: 576px) {
      .d-card {
        padding: 10px 6px !important;
      }
    }
  }
}


</style>