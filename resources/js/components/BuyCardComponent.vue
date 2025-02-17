<template>
  <div class="col-sm-6 col-md-4 col-lg-3">
    <div class="generic_content clearfix">
      <div class="generic_head_price clearfix">
        <div class="generic_head_content clearfix">
          <div class="head_bg"></div>
          <div class="head">
            <span class="text-white">{{ s_package.name }}</span>
          </div>
        </div>
        <div class="generic_price_tag clearfix">
                  <span class="price">
                    <del>  <span class="currency mr-2">${{ s_package.packageData.amount }}</span></del>
                    <span class="currency"><span class="sign">${{ s_package.packageData.discount_price }}</span></span>
                  </span>
        </div>
      </div>
      <div class="generic_feature_list">
        <ul>
          <li>{{ s_package.packageData.hours }}<span> Hours</span></li>
          <li>PDIO
            <span v-if="s_package.packageType.pdio">Included</span>
            <span v-else>Not Included</span>
          </li>
          <li>MTO
            <span v-if="s_package.packageType.mto">Included</span>
            <span v-else>Not Included</span>
          </li>
        </ul>
      </div>
      <div class="generic_price_btn clearfix">
        <router-link
            v-if="buy_now_link"
            :to="buy_now_link"
            exact-active-class="mal" target="blank"
        >Buy Now
        </router-link>
        <a
            v-if="external_link"
            :href="external_link"
            target="blank"
        >Buy Now</a>
      </div>
    </div>
  </div>
</template>
<script>

export default {
  props: [
    's_package',
    'buy_now_link',
    'external_link'
  ]

}
</script>
<style lang="scss">
.price {
  del {
    color: rgba(darkgray, 0.5);
    text-decoration: none;
    position: relative;
    font-size: 40px;
    font-weight: 100;

    span {
      color: rgba(darkgray, 0.8) !important;
    }

    &:before {
      content: " ";
      display: block;
      width: 100%;
      border-top: 3px solid rgba(darkgray, 0.8);
      height: 4px;
      position: absolute;
      bottom: 20px;
      left: 0;
      transform: rotate(-20deg);
    }

    &:after {
      content: " ";
      display: block;
      width: 100%;
      border-top: 3px solid rgba(darkgray, 0.8);
      height: 4px;
      position: absolute;
      bottom: 20px;
      left: 0;
      transform: rotate(20deg);
    }
  }
}
</style>
