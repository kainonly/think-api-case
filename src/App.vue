<template>
    <div id="app">
        <nut-navbar :leftShow="false"
                    :rightShow="false">
            vue-bit-nutui
        </nut-navbar>
        <router-view/>
        <nut-tabbar @tab-switch="tabSwitch"
                    :tabbarList="tabLists"
                    :bottom="true">
        </nut-tabbar>
    </div>
</template>

<script lang="ts">
    import {Vue, Component} from "vue-property-decorator";

    @Component
    export default class App extends Vue {
        tabIndex = 0;

        tabLists = [
            {
                tabTitle: "场景模拟",
                curr: true,
                icon: "./img/changjingguanli.svg",
                activeIcon: "./img/changjingguanli_active.svg",
                href: "###",
            },
            {
                tabTitle: "服务调试",
                curr: false,
                icon: "./img/tiaoshi.svg",
                activeIcon: "./img/tiaoshi_active.svg",
                href: "###",
            }
        ];

        tabSwitch(value: any, index: number) {
            this.tabIndex = index;
        };
    }
</script>

<style lang="scss">
</style>
