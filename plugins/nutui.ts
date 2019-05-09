import Vue from 'vue';
import NutUI from '@nutui/nutui';
import '@nutui/nutui/dist/nutui.css';

export default () => {
  NutUI.install(Vue, {});
};
