<template>
  <div class="d-container">
    <section class="py-64 pb-0 pb-lg-5">
      <div class="row">
        <div class="col-lg-7">
          <div class="intro_wraper">
            <div class="header text-lg-left ml-lg-3 ml-md-3 text-center">
              <h5>Are you looking for in-car driving lessons ?</h5>
            </div>
            <div class="icons text-center">
              <div class="icon">
                <i class="mdi mdi-clipboard-account"></i>
                <span>Private in-car lessons.</span>
              </div>
              <div class="icon">
                <i class="mdi mdi-train-car"></i>
                <span>Prepare for an upcoming road test.</span>
              </div>
              <div class="icon">
                <i class="mdi mdi-poll-box"></i>
                <span>Enhance your defensive driving skills.</span>
              </div>
              <div class="icon">
                <i class="mdi mdi-car-back"></i>
                <span>Car rental for road test </span>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-5 text-lg-right text-center">
          <img
            alt="Home hero background"
            class="img-fluid"
            src="/assets/images/bg/home-hero-bg.png"
          />
        </div>
      </div>
    </section>
  </div>
</template>

<script>
export default {
  name: "Intro",
};
</script>

<style scoped lang="scss">
section {
  flex-direction: row;
  box-sizing: border-box;
  display: flex;
  place-content: center space-between;
  align-items: center;

  .intro_wraper {
    .icons {
      flex-flow: row wrap;
      box-sizing: border-box;
      display: flex;

      .icon {
        margin-right: 15%;
        flex-direction: column;
        box-sizing: border-box;
        display: flex;
        place-content: stretch flex-start;
        align-items: stretch;
        flex: 1 1 100%;
        max-width: 30%;
        width: 200px;
        text-align: center;
        row-gap: 10px;

        .mdi {
          color: #36c;
          font-size: 30px;
          padding: 50px;
          margin: auto;
          box-shadow: 0 30px 40px #d4d9e833;
        }

        span {
          width: 100%;
          height: 50px;
          color: rgba(0, 0, 0, 0.87);
        }
      }
      @media (max-width: 960px) {
        .icon {
          margin-right: 10%;
        }
      }
      @media (max-width: 600px) {
        .icon {
          margin-right: 0;
          max-width: 50%;
        }
      }
    }
    h4 {
      margin-top: 40px;
      margin-bottom: 32px !important;
      font-size: 16px;
    }
    .search-btn {
      background: #3f54d1;
      color: #fff;
      font-size: 17px;
      cursor: pointer;
      border: none;
      border-radius: 6px;
      padding: 10px 20px;
      text-decoration: none !important;
    }
    .header {
      h5 {
        font-weight: 700;
        font-size: 25px;
        line-height: 49px;
        text-transform: capitalize;
        margin-bottom: 10px;
      }
    }
  }
}
</style>