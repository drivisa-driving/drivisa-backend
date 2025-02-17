<template>
  <section>
    <div class="bg-color">
      <div class="">
        <div class="logo-background"></div>
        <div class="mx-1">
          <div class="mat_card">
            <div class="mat_header">
              <div class="text-sm-left text-center">
                <div class="mat_card_title">
                  <h5>
                    Are you interested in becoming a driving <br />
                    instructor?
                  </h5>
                </div>
                <div class="mat_card_sub_title">
                  <p>To get your Ontario Provincial Instructor License:</p>
                </div>
              </div>
              <div class="content text-md-left text-center">
                <p>
                  PDIO’s MTO approved In-Car Instructor course contains 80 hours
                  of classroom content and 51.5 hours of in-car instruction. Due
                  to the COVID-19 pandemic, they are currently teaching using a
                  Virtual Classroom and offering modified in-car instruction to
                  follow Public Health guidelines. For information regarding
                  pre-requisites, course dates, cost of training and other
                  pertinent information, please visit the
                  <router-link
                    to="https://www.pdio.ca/driving-instructor-courses/"
                    target="_blank"
                    class="link-text"
                    >PROFESSIONAL DRIVERS OF ONTARIO website.
                  </router-link>
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: "OntarioComponent",
};
</script>

<style scoped lang="scss">
section {
  .bg-color {
    background: linear-gradient(to bottom right, #3e5ad2 50%, transparent 50.5%)
        no-repeat bottom,
      linear-gradient(0deg, #3f5bd2, #3f54d1) no-repeat top;
    color: #fff;
    padding-bottom: 3em;
    background-size: 100% 25em, 100% calc(100% - 25em);
    display: block;
    width: 100%;
    height: 650px;
    position: relative;
    contain: layout;

    .logo-background {
      background-image: url(/assets/images/logo-background.svg);
      width: 100%;
      position: absolute;
      top: 0;
      left: -56px;
      opacity: 0.1;
      height: 100%;
      background-repeat: no-repeat;
      background-position-x: 11%;
      background-position-y: bottom;
      background-size: 600px 600px;
      z-index: 1;
    }

    .mat_card {
      max-width: 920px;
      margin: 6rem auto;
      padding: 4rem 10rem;
      border-radius: 16px;
      background-color: hsla(0, 0%, 100%, 0.9);
      z-index: 2;
      transition: box-shadow 280ms cubic-bezier(0.4, 0, 0.2, 1);
      display: block;
      position: relative;
      color: rgba(0, 0, 0, 0.87);
      box-shadow: 0 2px 1px -1px rgba(0, 0, 0, 0.2),
        0 1px 1px 0 rgba(0, 0, 0, 0.14), 0 1px 3px 0 rgba(0, 0, 0, 0.12);

      .mat_header {
        margin: 0 16px;
        flex-direction: row;

        .mat_card_title {
          margin-bottom: 12px;
          display: block;

          h5 {
            font-size: 20px;
            font-weight: 500;
          }
        }

        .mat_card_sub_title {
          display: block;
          margin-bottom: 16px;
          font-size: 14px;
          color: rgba(0, 0, 0, 0.54);
        }
        .content {
          p {
            line-height: 35px;
            margin: 0 0 12px;
            font-size: 14px;
            .link-text {
              color: #3e5ad2;
              font-weight: bolder;
              text-decoration: none !important;
            }
          }
        }
      }
    }
    @media (max-width: 834px) {
      .mat_card {
        padding: 2rem 0;
      }
      .logo-background {
        background-size: 400px 400px;
      }
    }
    @media (max-width: 576px) {
      .logo-background {
        background-size: 200px 200px !important;
        margin-top: 20px !important;
        z-index: 3 !important;
      }
    }
  }
}
</style>