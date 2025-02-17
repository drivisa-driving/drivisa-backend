<template>
  <section>
    <div class="overlay"></div>
    <div class="d-container">
      <div class="row">
        <div class="col-lg-6">
          <article>
            When you join our team of professionals, you’re a valued and
            empowered employee whose contribution to our student’s future is
            based on excellence and integrity.Your passion will provide you with
            great personal satisfaction and a stable life.
          </article>
        </div>
        <div class="col-lg-6">
          <div class="Qualifications">
            <h5>Qualifications required :</h5>
            <ul class="list-unstyled">
              <li>
                <i class="mdi mdi-check"></i>
                Ontario Provincial In-Car Instructor license
              </li>
              <li>
                <i class="mdi mdi-check"></i>
                Must provide clean CPIC and/or vulnerable sector check
              </li>
              <li>
                <i class="mdi mdi-check"></i>
                Clean drivers abstract
              </li>
              <li>
                <i class="mdi mdi-check"></i>
                Access to personal vehicle certified for driving instruction
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: "QualificationsreqCompoent",
};
</script>

<style scoped lang="scss">
section {
  margin-top: 140px;
  display: block;
  background-image: url(/assets/images/qualifications-section-bg.jpg);
  background-repeat: no-repeat;
  background-size: cover;
  padding-top: 160px !important;
  padding-bottom: 160px !important;
  background-position: 0 100%;
  position: relative;

  .overlay {
    background-color: rgba(0, 0, 0, 0.5);
    position: absolute;
    left: 0 !important;
    top: 0 !important;
    width: 100% !important;
    height: 100% !important;
  }

  article {
    font-size: 24px;
    line-height: 44px;
    color: #fff;
  }

  .Qualifications {
    height: 100%;
    color: #fff;
    line-height: 2rem;
    padding: 5px;
    h5 {
      font-size: 25px;
      line-height: 49px;
      text-transform: capitalize;
      margin-bottom: 16px;
    }
    li {
      font-size: 20px;
      line-height: 35px;

      .mdi {
        color: #05a100;
        font-size: 24px;
        height: 24px;
        width: 24px;
        font-weight: 400 !important;
        line-height: 1;
      }
    }
  }
}
@media (max-width: 992px) {
  section {
    margin-top: 80px;
    padding-top: 80px !important;
    padding-bottom: 60px !important;

    article {
      margin-bottom: 100px;
      padding: 0 20px;
    }
    .Qualifications {
      padding: 0 20px;
    }
  }
}
</style>