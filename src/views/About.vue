<template>
    <div class="about">
        <p>This is an about page</p>
        <nut-button @click="close()">
            隐藏
        </nut-button>
    </div>
</template>

<script lang="ts">
    import {Vue, Component} from "vue-property-decorator";
    import {NutToast} from "../types/nut-toast";

    @Component
    export default class About extends Vue {
        private toast: NutToast | any;

        mounted() {
            this.toast = this.$toast.text("msg", {duration: 0});
        }

        close() {
            this.toast.hide();
        }
    }
</script>
