<template>
  <section class="container">
    <div>
      <logo/>
    </div>
  </section>
</template>

<script lang="ts">
  import {Vue, Component} from 'vue-property-decorator';
  import Logo from '~/components/Logo.vue'

  @Component({
    components: {
      Logo
    }
  })
  export default class Index extends Vue {
    test() {

    }
  }
</script>
