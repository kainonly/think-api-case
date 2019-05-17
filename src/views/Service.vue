<template>
    <div>
        <nut-noticebar
                text="NutUI是一套京东风格的移动端Vue组件库，开发和服务于移动Web界面的企业级前中后台产品">
        </nut-noticebar>
        <h4>微信公众号</h4>
        <nut-cell title="微信授权" :isLink="true" :showIcon="true"
                  @click.native="oauth()"></nut-cell>

        <nut-cell title="微信支付" :showIcon="true"></nut-cell>
        <h4>高德地图</h4>
        <nut-cell title="唤醒组件" :showIcon="true"></nut-cell>
    </div>
</template>

<script lang="ts">
    import {Vue, Component, Mixins} from "vue-property-decorator";

    @Component
    export default class About extends Vue {
        oauth() {
            console.log("asd");
        }
    }
</script>
