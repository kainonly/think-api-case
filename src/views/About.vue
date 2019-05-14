<template>
    <div class="about">
        <p>This is an about page</p>
        <nut-button @click="test">
            去结算
        </nut-button>
    </div>
</template>

<script lang="ts">
    import {Vue, Component} from "vue-property-decorator";

    @Component
    export default class About extends Vue {
        test() {
            this.$dialog({
                title: "asd",
                content: "xxx",
                onOkBtn() {
                    this.close();
                }
            });
        }
    }
</script>
