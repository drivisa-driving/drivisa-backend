<template>
  <section>
    <div class="d-container">
      <div class="row">
        <div class="col-lg-5">
          <div class="text-center">
            <img
              src="/assets/media/imgs/iphone.png"
              alt="iphone"
              class="img-fluid"
            />
          </div>
        </div>
        <div class="col-lg-7">
          <div
            class="
              store-section
              d-md-flex
              flex-md-column
              justify-content-md-between
            "
          >
            <div class="text-lg-left text-center">
              <h5>Check out our app on the store</h5>
              <p class="mt-lg-5">
                For athletes, high altitude produces two contradictory effects
                on performance. For explosive events (sprints up to 400 metres,
                long jump, triple jump) the reduction in atmospheric pressure
                means there is less resistance from the atmosphere and the
                athlete's performance will generally be better at high altitude.
              </p>
            </div>
            <div class="store-imgs text-lg-left text-center">
              <img
                src="/assets/media/imgs/download_on_the_App_Store_Badge 1.png"
                alt="download_on_the_App_Store_Badge"
              />
              <br />
              <img
                class="mt-3"
                src="/assets/media/imgs/google-play.png"
                alt="google-play"
              />
            </div>
            <div></div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: "AppStoreComponent",
};
</script>

<style scoped lang="scss">
section {
  margin-top: 10rem;
  .store-section {
    height: 762px !important;
    h5 {
      font-size: clamp(20px, 8vw, 44px);
      font-weight: 700 !important;
      line-height: 50px;
      text-transform: capitalize;
      word-spacing: 10px;
    }
  }
  @media (max-width: 765px) {
    .store-section {
      height: 550px !important;

      .store-imgs {
        margin-top: 100px;
      }
    }
  }
}
</style>