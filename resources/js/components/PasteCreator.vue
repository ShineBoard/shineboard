<template>
    <pre class="paste-creator">
        <code @input="highlight" :class="'editable ' + this.languageClass" id="editable" contenteditable placeholder="Paste your code..." spellcheck="false" autofocus></code>
    </pre>
</template>

<script>
    import hljs from 'highlight.js';
    import { saveCaretPosition } from '../utils/Caret';

    export default {

        props: ['language', 'change'],
        computed: {

            languageClass() {

                return `language-${this.language}`;
            },
        },
        methods: {

            highlight() {

                const element = document.getElementById('editable');
                const restore = saveCaretPosition(element);

                this.change(element.innerText);

                element.innerHTML = element.innerText;

                hljs.highlightBlock(element);

                restore();
            },
        },
        mounted() {

            document.querySelector('#editable').addEventListener('keydown', (event) => {

                if(event.keyCode === 9) {

                    document.execCommand('insertHTML', false, '&#009');
                    event.preventDefault();

                } else if(event.keyCode === 13) {

                    document.execCommand('insertHTML', false, '&#010');
                    event.preventDefault();
                }
            });

            this.highlight();
        },
        watch: {

            language() {

                this.highlight();
            }
        }
    }
</script>

<style lang="scss">
    @import '../../sass/themes/default';

    .paste-creator {

        height: 100%;
        margin: 0;
        white-space: nowrap;

        .editable {

            display: block;
            width: 100%;
            height: 100%;
            overflow: scroll;

            &[placeholder]:empty:before {

                content: attr(placeholder);
                color: $comment;
            }
        }
    }
</style>
