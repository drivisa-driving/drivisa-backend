<template>
  <div class="container-box">
    <div>
      <div class="loading" data-loading-text="Loading..."></div>
      <div class="spinception"></div>
    </div>
  </div>
</template>

<script>
export default {
  name: "LoadingComponent"
}
</script>

<style scoped lang="scss">

.container-box {
  box-sizing: border-box;
  display: flex;
  justify-content: center;
  align-items: center;
  width: 100vw;
  height: 100%;
  background-color: #fff;
  text-align: center;
}

.loading {
  text-align: center;
  font-size: 36px;
  font-family: serif;
  font-weight: bold;
  letter-spacing: 4.4px;
  text-transform: capitalize;
  overflow: hidden;
  transform: translate(0%, 0%);

  &:before {
    color: #00ff9f;
    content: attr(data-loading-text);
  }

  &:after {
    top: 0;
    left: 0;
    width: 0;
    opacity: 1;
    color: #0094ff;
    overflow: hidden;
    position: absolute;
    content: attr(data-loading-text);
    animation: loading 5s infinite;
  }

  @keyframes loading {
    0% {
      width: 0;
    }
    100% {
      width: 100%;
    }
  }
}

.spinception {
  box-sizing: border-box;
  position: relative;
  margin: auto;
  width: 128px;
  aspect-ratio: 1/1;
  border-radius: 50%;
  border: none;
  border-top: 16px solid #0094ff;
  border-bottom: 16px solid #0094ff;
  animation: spinception 2s linear infinite;
}

.spinception::before {
  box-sizing: border-box;
  position: absolute;
  inset: 32px;
  margin: auto;
  content: "";
  aspect-ratio: 1/1;
  border-radius: 50%;
  border: none;
  border-top: 16px solid #00ff9f;
  border-bottom: 16px solid #00ff94;
  animation: spinception 1s linear infinite;
  animation-direction: reverse;
}

.spinception::after {
  box-sizing: border-box;
  position: absolute;
  inset: 48px;
  margin: auto;
  content: "";
  aspect-ratio: 1/1;
  border-radius: 50%;
  border: none;
  border-top: 8px solid #0094ff;
  border-bottom: 8px solid #0094ff;
  animation: spinception 4s linear infinite;
}

@keyframes spinception {
  from {
    transform: rotateZ(0deg);
  }
  to {
    transform: rotateZ(360deg);
  }
}


</style>