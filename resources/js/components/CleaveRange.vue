<template>
    <vue-cleave
        class="form-control t-a:r"
        v-model.number="m_value"
        type="number"
        step="5"
        :options="{ numeral: true, numeralDecimalMark: ',', delimiter: '.' }"
    />
</template>

<script>
import VueCleave from "vue-cleave-component";

export default {
    components: { VueCleave },
    props: ["value", "min", "max"],

    data() {
        return {
            m_value: this.value,
        }
    },

    watch: {
        m_value() {
            if (this.m_value < this.min) {
                this.m_value = this.min
            }
            else if (this.m_value > this.max) {
                this.m_value = this.max
            }

            this.$emit('input', this.m_value);
        }
    }
};
</script>
