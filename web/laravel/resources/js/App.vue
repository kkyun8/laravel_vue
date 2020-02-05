<template>
  <div>
    <main>
      <div class="container">
        <RouterView />
      </div>
    </main>
  </div>
</template>

<script>
import Navbar from './components/Navvar'
import Footer from './components/Footer'

export default {
  components: {
    Navbar,
    Footer
  }
}
</script>