import Vue from 'vue';

interface NutuiInstance {
  text(msg: string): any;
}

declare module 'vue/types/vue' {
  interface Vue {
    $toast: NutuiInstance;
  }
}
