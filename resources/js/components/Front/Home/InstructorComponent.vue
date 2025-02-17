<template>
  <section>
    <div class="d-container">
      <div class="row">
        <div class="col-lg-12 col-12">
          <div class="instructors-section text-center">
            <h5>Are you a licensed driving instructor in Ontario?</h5>
            <p>Join Drivisa family and take advantage of the benefits.</p>
          </div>
          <div class="icons">
            <div class="icon">
              <i class="mdi mdi-cash"></i>
              <span>Make up to $76,000 on regular hours</span>
            </div>
            <div class="icon">
              <i class="mdi mdi-timer-sand-full"></i>
              <span>Flexible hours</span>
            </div>
            <div class="icon">
              <i class="mdi mdi-clock-outline"></i>
              <span>Set your schedule and go</span>
            </div>
            <div class="icon">
              <i class="mdi mdi-account-supervisor"></i>
              <span>Be your own boss</span>
            </div>
            <div class="icon">
              <i class="mdi mdi-emoticon-outline"></i>
              <span>Eliminate inconvenience</span>
            </div>
            <div class="icon">
              <i class="mdi mdi-cash-multiple"></i>
              <span>Have control over your earning potential</span>
            </div>
          </div>
        </div>
        <!-- <div class="col-lg-6">
          <div class="embed-responsive embed-responsive-16by9">
            <iframe
              class="embed-responsive-item"
              src="//www.youtube.com/embed/zB4I68XVPzQ"
            ></iframe>
          </div>
        </div> -->
      </div>
    </div>
  </section>
</template>

<script>
export default {
  name: "InstructorComponent",
};
</script>

<style scoped lang="scss">
section {
  .instructors-section {
    h5 {
      font-weight: 700;
      font-size: 23px;
      line-height: 49px;
      text-transform: capitalize;
      margin-bottom: 10px;
      color: rgba(0, 0, 0, 0.87);
    }
    p {
      margin: 0 0 12px;
    }
  }
  .icons {
    flex-flow: row wrap;
    box-sizing: border-box;
    display: flex;

    .icon {
      flex-direction: column;
      box-sizing: border-box;
      display: flex;
      place-content: stretch flex-start;
      align-items: stretch;
      flex: 1 1 100%;
      max-width: 33%;
      width: 400px;
      text-align: center;
      row-gap: 10px;

      .mdi {
        color: #36c;
        font-size: 50px;
        padding: 50px;
        margin: auto;
        box-shadow: 0 30px 40px #cdced333;
      }

      span {
        width: 100%;
        height: 50px;
        color: rgba(0, 0, 0, 0.87);
      }
    }
    @media (max-width: 600px) {
      .icon {
        margin-right: 0;
        max-width: 50%;
      }
    }
  }
}
</style>