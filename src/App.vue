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
        tabLists = [
            {
                tabTitle: "场景模拟",
                curr: true,
                icon: "./img/changjingguanli.svg",
                activeIcon: "./img/changjingguanli_active.svg",
            },
            {
                tabTitle: "服务调试",
                curr: false,
                icon: "./img/tiaoshi.svg",
                activeIcon: "./img/tiaoshi_active.svg",
            }
        ];

        tabSwitch(value: any, index: number) {
            switch (index) {
                case 0:
                    this.$router.push("/");
                    break;
                case 1:
                    this.$router.push("/about");
                    break;
            }
        };
    }
</script>

<style lang="scss">
    body {
        height: 100%;
        width: 100%;
        margin: 0;
        background: rgb(246, 246, 246);
    }
</style>
