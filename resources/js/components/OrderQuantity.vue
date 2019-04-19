<template>
    <span>
        {{ p_quantity }}
    </span>
</template>

<script>

export default {
    props: [
        "value",
    ],

    data() {
        return {
            p_quantity: this.value
        }
    },

    watch: {
        value(val) {
            if (val >= 0) {
                this.p_quantity = val
            }
            else {
                this.p_quantity = 0
            }

            this.$emit("input", this.p_quantity)
        }
    }
}
</script>
