<template>
    <v-dialog
        v-model="dialog"
        persistent
        max-width="600px"
    >
        <v-card
        :loading="loading"
        >
            <v-card-title>
                Edit rule
                <v-spacer></v-spacer>
                <v-btn
                    text
                    color="primary"
                    class="mr-2"
                    @click="editor.chain().focus().toggleCodeBlock().run()"
                >
                    Code
                </v-btn>
                <v-btn
                    text
                    color="primary"
                    @click="dialog = false"
                >
                    <v-icon>
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <v-form
                    ref="form"
                >
                    <v-text-field
                        label="Name"
                        v-model="importRule.name"
                        :error-messages="errors.name"
                    >

                    </v-text-field>

                    <v-select
                        v-model="importRule.column"
                        label="Column"
                        :items="columns"
                        :error-messages="errors.columns"
                    >

                    </v-select>

                    <editor-content :editor="editor" />
                </v-form>
            </v-card-text>
            <v-card-actions>
                <v-btn
                    text
                    color="primary"
                    :disabled="loading"
                    @click="submit"
                >
                    Store
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>

</template>

<script>
import { Editor, EditorContent, VueNodeViewRenderer } from '@tiptap/vue-2'
import Document from '@tiptap/extension-document'
import Paragraph from '@tiptap/extension-paragraph'
import Text from '@tiptap/extension-text'
import CodeBlockLowlight from '@tiptap/extension-code-block-lowlight'
import CodeBlockComponent from './CodeBlockComponent'

import lowlight from 'lowlight'

import Bus from "../../../bus";
import {mapActions, mapGetters} from 'vuex';

export default {
name: "EditImportRuleDialog",
    components: {
        EditorContent,
    },
    data() {
        return {
            loading: true,
            columns: ['cat_level_1', 'cat_level_2', "cat_level_3"],

            editor: null,
            errors: []
        }
    },

    props: {
        showDialog: Boolean
    },


    computed: {
        ...mapGetters({
            importRule: 'imports/importRule',
        }),
        dialog:{
            get(){
                return this.showDialog
            },
            set(val){
                return this.$emit("update:showDialog", val);
            }
        },
    },

    methods: {
        ...mapActions({
            setImport: 'imports/setImport'
        }),

        submit() {
            const self = this
            this.loading = true
            this.errors = []
            this.importRule.script = this.editor.getHTML()
            axios.patch(`/api/importRules/${self.importRule.id}`, this.importRule).then((response) => {
                self.loading = false
                self.$refs.form.reset()
                Bus.$emit('importRules:updated');
                self.dialog = false
            }).catch((error) => {
                this.errors = error.response.data.errors
                self.loading = false
            })
        },

    },

    mounted() {
    const self = this
        this.loading = false
        this.editor = new Editor({
            extensions: [
                Document,
                Paragraph,
                Text,
                CodeBlockLowlight.configure({
                    lowlight,
                }),
            ],
            content: self.importRule.script
        })


    },
    watch: {
        editor: function (val) {
            console.log(this.editor.getHTML())
        },

    }
}

</script>



<style lang="scss">
                      /* Basic editor styles */
                  .ProseMirror {
> * + * {
    margin-top: 0.75em;
}

pre {
    background: #0D0D0D;
    color: #FFF;
    font-family: 'JetBrainsMono', monospace;
    padding: 0.75rem 1rem;
    border-radius: 0.5rem;

code {
    color: inherit;
    padding: 0;
    background: none;
    font-size: 0.8rem;
}

.hljs-comment,
.hljs-quote {
    color: #616161;
}

.hljs-variable,
.hljs-template-variable,
.hljs-attribute,
.hljs-tag,
.hljs-name,
.hljs-regexp,
.hljs-link,
.hljs-name,
.hljs-selector-id,
.hljs-selector-class {
    color: #F98181;
}

.hljs-number,
.hljs-meta,
.hljs-built_in,
.hljs-builtin-name,
.hljs-literal,
.hljs-type,
.hljs-params {
    color: #FBBC88;
}

.hljs-string,
.hljs-symbol,
.hljs-bullet {
    color: #B9F18D;
}

.hljs-title,
.hljs-section {
    color: #FAF594;
}

.hljs-keyword,
.hljs-selector-tag {
    color: #70CFF8;
}

.hljs-emphasis {
    font-style: italic;
}

.hljs-strong {
    font-weight: 700;
}
}
}
</style>
