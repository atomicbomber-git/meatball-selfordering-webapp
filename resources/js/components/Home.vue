<template>
    <div class="row">
        <div class="col-lg-9">

                <div class="card">
                    <div class="card-body">
                        <transition name="menu-fade" mode="out-in">
                            <div key="if" v-if="selected_menu_category === null">
                                <div class="row d-flex justify-content-center">
                                    <div class="card col-md-3 mt-3 mr-3" v-for="menu_category in menu_data" :key="menu_category.id">
                                        <img class="card-img-top"
                                            :src="`/menuCategory/image/${menu_category.id}`"
                                            :alt="menu_category.name"
                                            />
                                        <div class="card-body">
                                            <span class="font-weight-bold text-info">
                                                {{ menu_category.name }}
                                            </span>

                                            <p class="text-muted">
                                                Lorem ipsum dolor, sit amet consectetur adipisicing elit. Quidem saepe ab, molestiae obcaecati nisi praesentium repellat.
                                            </p>
                                            
                                            <div class="text-right">
                                                <button @click="onOrderMenuCategoryButtonClick(menu_category)" class="btn btn-info btn-sm">
                                                    Pesan
                                                    <i class="fa fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div key="else" v-else @click="selected_menu_category = null">
                                <div>
                                    <div class="text-right">
                                        <button class="btn btn-warning">
                                            <i class="fa fa-arrow-left"></i>
                                            Kembali
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </transition>
                    </div>
                </div>
        </div>

        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    <h1 class="h5"> Daftar Pesanan </h1>
                    <hr>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    props: [
        "menu_data",
        "submit_url", "redirect_url",
    ],

    data() {
        return {
            p_menu_data: this.menu_data.map(menu_categories => ({
                ...menu_categories,
                menu_items: menu_categories.menu_items.map(menu_item => ({
                    ...menu_item,
                    is_ordered: false,
                }))
            })),

            selected_menu_category: null,
        }
    },

    methods: {
        onOrderMenuCategoryButtonClick(menu_category) {
            this.selected_menu_category = menu_category
        }
    }
}
</script>

<style scoped>
    img {
        width: 240px;
        height: 240px;
        object-fit: cover;
    }

    .menu-fade-enter-active, .menu-fade-leave-active {
        transition: opacity .1s ease;
    }
    .menu-fade-enter, .menu-fade-leave-to
    /* .menu-fade-leave-active below version 2.1.8 */ {
        opacity: 0;
    }
</style>
