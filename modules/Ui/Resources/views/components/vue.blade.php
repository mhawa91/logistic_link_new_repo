<script>
    function makeid(length) {
        var result           = [];
        var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        var charactersLength = characters.length;
        for ( var i = 0; i < length; i++ ) {
            result.push(characters.charAt(Math.floor(Math.random() * charactersLength)));
        }
        return result.join('');
    }

    Vue.component('ui-inline-form-group', {
        props: ['id', 'label', 'description'],
        template: `
            <div class="row g-3 align-center">
                <div class="col-lg-4">
                    <div class="form-group">
                        <label class="form-label" :for="getId">@{{ label }}</label>
                        <span class="form-note" v-if="description">@{{ description }}</span>
                    </div>
                </div>
                <div class="col-lg-8">
                    <slot></slot>
                </div>
            </div>
        `,
        mounted   : function() {},
        destroyed : function() {},
        watch     : {},
        methods   : {},
        computed  : {
            getId: function() {
                this.id = this.id || makeid(10);
                return this.id;
            }
        }
    });

    Vue.component('ui-inline-input', {
        props: ['id', 'name', 'label', 'description', 'value', 'placeholder'],
        template: `
            <ui-inline-form-group
                :id="getId"
                :label="label"
                :description="description">
                <div class="form-group">
                    <div class="form-control-wrap">
                        <input type="text" :name="name" class="form-control" :id="getId" :value="value" v-model="value" :placeholder="placeholder">
                        <slot></slot>
                    </div>
                </div>
            </ui-inline-form-group>
        `,
        mounted   : function() {},
        destroyed : function() {},
        watch     : {
            value: function(newValue, oldValue) {
                this.$emit('change', {
                    newValue: newValue, 
                    oldValue: oldValue,
                });
            }
        },
        methods   : {},
        computed  : {
            getId: function() {
                this.id = this.id || makeid(10);
                return this.id;
            }
        }
    });

    Vue.component('ui-inline-textarea', {
        props: ['id', 'name', 'label', 'description', 'value', 'placeholder', 'rows'],
        template: `
            <ui-inline-form-group
                :id="getId"
                :label="label"
                :description="description">
                <div class="form-group">
                    <div class="form-control-wrap">
                        <textarea :name="name" class="form-control" :id="getId" :value="value" v-model="value" :placeholder="placeholder" :rows="getRows">@{{value}}</textarea>
                        <slot></slot>
                    </div>
                </div>
            </ui-inline-form-group>
        `,
        mounted   : function() {},
        destroyed : function() {},
        watch     : {
            value: function(newValue, oldValue) {
                this.$emit('change', {
                    newValue: newValue, 
                    oldValue: oldValue,
                });
            }
        },
        methods   : {},
        computed  : {
            getId: function() {
                this.id = this.id || makeid(10);
                return this.id;
            },
            getRows: function() {
                this.rows = this.rows || 5;
                return this.rows;
            }
        }
    });

    Vue.component('ui-inline-radios-or-checkboxes', {
        props: ['id', 'name', 'label', 'description'],
        template: `
            <ui-inline-form-group
                :id="getId"
                :label="label"
                :description="description">
                <ul class="custom-control-group g-3 align-center flex-wrap">
                    <slot></slot>
                </ul>
            </ui-inline-form-group>
        `,
        mounted   : function() {},
        destroyed : function() {},
        watch     : {},
        methods   : {},
        computed  : {
            getId: function() {
                this.id = this.id || makeid(10);
                return this.id;
            }
        }
    });

    Vue.component('ui-inline-multi-radio', {
        props: ['id', 'name', 'label', 'value', 'selected'],
        template: `
            <li>
                <div class="custom-control custom-radio" v-bind:class="{'checked': selected == value}">
                    <input type="radio" :name="name" class="custom-control-input" :id="getId" :value="value" :checked="selected == value" @change="setSelected">
                    <label class="custom-control-label" :for="getId">@{{ label }}</label>
                </div>
            </li>
        `,
        mounted   : function() {},
        destroyed : function() {},
        watch     : {},
        methods   : {
            setSelected: function($event) {
                this.$emit('change', {
                    newValue: $event.target.value, 
                    oldValue: this.selected,
                });
                this.selected = $event.target.value;
            }
        },
        computed  : {
            getId: function() {
                this.id = this.id || makeid(10);
                return this.id;
            }
        }
    });

    Vue.component('ui-inline-multi-switch', {
        props: ['id', 'name', 'label', 'value', 'selected', 'type'],
        template: `
            <li>
                <div class="form-group">
                    <div class="custom-control custom-switch" v-bind:class="{'checked': selected == value}">
                        <input :type="type || 'checkbox'" :name="name" class="custom-control-input" :id="getId" :value="value" :checked="selected == value" @change="setSelected">
                        <label class="custom-control-label" :for="getId">@{{ label }}</label>
                    </div>
                </div>
            </li>
        `,
        mounted   : function() {},
        destroyed : function() {},
        watch     : {},
        methods   : {
            setSelected: function($event) {
                this.$emit('change', {
                    newValue: $event.target.value, 
                    oldValue: this.selected,
                });
                this.selected = $event.target.value;
            }
        },
        computed  : {
            getId: function() {
                this.id = this.id || makeid(10);
                return this.id;
            }
        }
    });

    Vue.component('ui-select2', {
        template: `
            <select class="s2-init">
                <slot></slot>
            </select>
        `,
        props: ['selected'],
        data : function () {
            return {
                options  : {},
            }
        },
        mounted: function() {
            this.mount();
        },
        beforeDestroy: function() {
            this.unmount();
        },
        watch: {
            selected: function(value) {
                // console.log(value);
                this.setValue(value);
            }
        },
        methods: {
            setValue: function(selected) {
                $(this.$el).append(
                    $("<option selected='selected'></option>")
                    .val(selected ? selected.id : '0')
                    .text(selected ? selected.text : '-- Select --')
                ).trigger('change');
                this.$emit("change", selected);
            },
            unmount: function() {
                $(this.$el).select2("destroy");
            },
            mount: function() {
                var vm = this;
                this.$emit('options', this.options)
                $(this.$el)
                .select2(this.options)
                .trigger("change")
                .on("select2:select", function() {
                    var selected = $(this).select2('data')[0];
                    vm.setValue(selected);
                });
                if(this.selected) {
                    this.setValue(this.selected);
                } 
            }
        },
    });

    Vue.component('ui-datepicker', {
        template: `
            <input type="text" :value="value" class="form-control" readonly style="background-color: #fff;">
        `,
        props   : ['value'],
        data    : function () {
            return {
                options: {
                    format         : 'yyyy-mm-dd',
                    rtl            : $('body').attr('dir') == 'rtl',
                    todayBtn       : "linked",
                    orientation    : 'bottom left',
                    // templates      : arrows,
                    autoclose      : true,
                    clearBtn       : true,
                    todayHighlight : true,
                }
            }
        },
        mounted : function() {
            var vm = this;
            // this.$emit('options', this.options)
            $(this.$el)
            .datepicker(this.options)
            .on('change', function(e) {
                vm.setValue($(e.target).val());
            });
            if(this.value) {
                this.$emit("mountedValue", this.value);
            } 
        },
        beforeDestroy: function() {
            // $(this.$el).select2("destroy");
        },
        watch: {
            value: function(value) {
                this.setValue(value);
            }
        },
        methods: {
            setValue: function(value) {
                this.$emit("value", value);
            }
        },
    });
</script>