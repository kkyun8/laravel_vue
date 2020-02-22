<template>
<div>

  <div id="test1" v-on:scroll="handleScroll_1" class="card" style="height: 100px; overflow-y:scroll">
    <div class="card-body">
      <h5 class="card-title">Card title</h5>
      <h6 class="card-subtitle mb-2 text-muted">Card subtitle</h6>
      <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
      <a href="#" class="card-link">Card link</a>
      <a href="#" class="card-link">Another link</a>
    </div>
  </div>
  <div id="test2" v-on:scroll="handleScroll_2" class="card test" style="height: 100px; overflow-y:scroll">
    <div class="card-body">
      <h5 class="card-title">Card title</h5>
      <h6 class="card-subtitle mb-2 text-muted">Card subtitle</h6>
      <p class="card-text">Some quick example text to build on the card title and make up the bulk of the card's content.</p>
      <a href="#" class="card-link">Card link</a>
      <a href="#" class="card-link">Another link</a>
    </div>
  </div>
</div>
</template>
<script>
export default {
  methods: {
    handleScroll_1 (event) {
      // Any code to be executed when the window is scrolled

      console.log("1: " + document.getElementById("test1").scrollTop);
      console.log("2: " + document.getElementById("test2").scrollTop);

      document.getElementById("test2").scrollTop = document.getElementById("test1").scrollTop
    },
       handleScroll_2 (event) {
      document.getElementById("test1").scrollTop = document.getElementById("test2").scrollTop
    },
  },
  created () {
    window.addEventListener('scroll', this.handleScroll);
  },
  destroyed () {
    window.removeEventListener('scroll', this.handleScroll);
  }
}
</script>
<style>
.test{
position: -webkit-sticly;
position: sticky;
top: 0;
}


</style>