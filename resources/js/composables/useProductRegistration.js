/**
 * Composable para gerenciamento do registro de produtos.
 * Contém os estados reativos, funções para chamadas de API,
 * lógica de upload de imagens via dropzone e manipulação do diálogo.
 */
import { ref } from 'vue';
import * as z from 'zod';
import { useDropzone } from 'vue3-dropzone';
import { dialogState } from '@/composables/useToggleDialog';
import renderToast from '@/components/renderPromiseToast';
import { StringUtil } from '@/util';
import { getCategories } from '@/services/api/categories';
import { createProduct, listProducts, updateProduct } from '@/services/api/products';
import axios from 'axios';

export function useProductRegistration() {
    // Estados reativos
    const isLoading = ref(true);
    const categories = ref([]);
    const imagesSrc = ref([]);
    const products = ref([]);
    const productDetails = ref({});
    const selectedProducts = ref([]);
    const defaultImgValue = { src: null, raw: null };
    const img = ref({ ...defaultImgValue });
    const disabledButton = ref(false);

    // Estado do diálogo (modal)
    const { isOpen, toggleDialog } = dialogState('RegisterProduct');

    // Configuração do dropzone para upload de imagens
    const onDrop = (acceptedFiles) => {
        if (acceptedFiles.length) {
            // Cria URL para pré-visualização da imagem
            const src = URL.createObjectURL(acceptedFiles[0]);
            img.value = { src, raw: acceptedFiles };
        }
    };
    const { getRootProps, getInputProps, open, ...dropzoneRest } = useDropzone({
        onDrop,
        multiple: false,
    });

    // Esquema de validação com Zod e Vee-Validate
    const formSchema = z.object({
        nome: z.string({ required_error: 'Informe o nome do produto' }),
        idCategoria: z.string({ required_error: 'Informe a categoria do produto' }),
        descricao: z.string({ required_error: 'Descreva o produto' }),
        img: z.string({ required_error: 'Selecione uma imagem' }),
        itensComposicao: z.array(z.string()).nullable().optional(),
    });

    /**
     * Abre ou fecha o diálogo e carrega os dados necessários.
     * Ao fechar, reseta os valores do formulário.
     */
    const handleDialogOpen = (openValue) => {
        if (!openValue) {
            resetDialogValues();
        }
        // Carrega categorias, imagens e produtos
        Promise.all([fetchCategories(), fetchImages(), fetchProducts()]).catch(console.error);
        toggleDialog();
    };

    /**
     * Busca as categorias e decodifica os nomes.
     */
    const fetchCategories = () => {
        return renderToast(
            getCategories(),
            'Carregando categorias...',
            'Categorias carregadas',
            'Erro ao carregar categorias',
            (response) => {
                categories.value = response.data.map((cat) => ({
                    ...cat,
                    nome: StringUtil.utf8Decode(cat.nome),
                }));
                isLoading.value = false;
            }
        );
    };

    /**
     * Busca as imagens disponíveis.
     */
    const fetchImages = () => {
        const promise = axios.get('/api/listImages');
        return renderToast(
            promise,
            'Carregando imagens...',
            'Imagens carregadas',
            'Erro ao carregar imagens',
            (response) => {
                imagesSrc.value = response.data.img.map((name) => `images/uploads/${name}`);
            }
        );
    };

    /**
     * Busca a lista de produtos e decodifica os nomes.
     */
    const fetchProducts = () => {
        return renderToast(
            listProducts(),
            'Carregando produtos...',
            'Produtos carregados',
            'Erro ao carregar produtos',
            (response) => {
                products.value = response.data.map((product) => ({
                    ...product,
                    nome: StringUtil.utf8Decode(product.nome),
                }));
            },
            (err)=>{
                console.log(err)
            }
        );
    };

    // Reseta a pré-visualização da imagem para o valor padrão
    const handleDefaultImage = () => {
        img.value = { ...defaultImgValue };
    };

    // Função para registrar um novo produto
    const registerProduct = (payload) => {
        return renderToast(
            createProduct(payload),
            'Salvando produto...',
            'Produto salvo',
            'Erro ao salvar produto'
        );
    };

    // Função para atualizar um produto existente
    const updateProductData = (productId, payload) => {
        return renderToast(
            updateProduct(productId, payload),
            'Atualizando produto...',
            'Produto atualizado',
            'Erro ao atualizar produto'
        );
    };

    /**
     * Realiza o upload da imagem e, a partir do retorno, chama a função
     * de registro ou atualização do produto.
     */
    const uploadImage = (fileImage, payload, productId) => {
        const formData = new FormData();
        formData.append('image', fileImage[0]);
        const promise = axios.post('/upload', formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
        });
        return renderToast(
            promise,
            'Salvando imagem...',
            'Imagem salva',
            'Erro ao salvar imagem',
            (response) => {
                const imgName = response.data.fineName;
                if (productId) {
                    updateProductData(productId, { ...payload, img: imgName });
                } else {
                    registerProduct({ ...payload, img: imgName });
                }
            }
        );
    };

    /**
     * Determina qual ação executar (upload, criação ou atualização)
     * com base nos detalhes do produto e na imagem.
     */
    const submitProductData = (productDetailsVal, payload, fileImage) => {
        const shouldUploadImage =
            productDetailsVal && fileImage && productDetailsVal.img !== fileImage[0].name;
        if (shouldUploadImage) return uploadImage(fileImage, payload, productDetailsVal.id);
        if (productDetailsVal) return updateProductData(productDetailsVal.id, payload);
        if (fileImage) return uploadImage(fileImage, payload);
        return registerProduct(payload);
    };

    /**
     * Executa a submissão do formulário após a validação.
     */
    const onSubmit = (values) => {
        const fileImage = img.value.raw;
        // Adiciona um indicador de composição caso itens de composição estejam presentes
        const payload = { ...values, composicao: values.itensComposicao?.length > 0 ? 1 : 0 };
        submitProductData(productDetails.value, payload, fileImage);
        fetchProducts();
    };

    /**
     * Manipula o evento de submit do formulário.
     * Valida os campos e chama a função onSubmit caso tudo esteja correto.
     */
    const handleSubmit = async (event, values, validate) => {
        event.preventDefault();
        const isValid = await validate();
        if (isValid.valid) onSubmit(values);
    };

    /**
     * Ao carregar a imagem, atualiza o campo 'img' com o nome do arquivo.
     */
    const handleImageLoad = (setFieldValue) => {
        const fileImage = img.value.raw;
        if (fileImage) setFieldValue('img', fileImage[0].name);
    };

    /**
     * Atualiza os campos do formulário ao selecionar um produto já cadastrado.
     */
    const handleUpdateSelect = async (productId, setFieldValue, validate) => {
        const selected = products.value.find((product) => product.id == productId);
        if (selected) {
            handleDefaultImage();
            // Atualiza os campos do formulário com os dados do produto selecionado
            const updates = {
                nome: StringUtil.utf8Decode(selected.nome),
                descricao: StringUtil.utf8Decode(selected.descricao),
                idCategoria: `${selected.idCategoria}`,
                img: selected.img,
            };
            Object.entries(updates).forEach(([field, value]) => setFieldValue(field, value));

            // Atualiza a pré-visualização da imagem mantendo os dados raw
            img.value = { src: `images/uploads/${selected.img}`, raw: [{ name: selected.img }] };
            setFieldValue('img', selected.img);

            // Valida os campos atualizados individualmente
            await Promise.all(Object.keys(updates).map((field) => validate(field)));

            // Se houver itens de composição, atualiza o campo e o estado
            if (selected.composicaoItens?.length) {
                const compositionIds = selected.composicaoItens.map(
                    (item) => `${item.id}-${item.quantidade}`
                );
                setFieldValue('itensComposicao', compositionIds);
                selectedProducts.value = selected.composicaoItens;
            } else {
                setFieldValue('itensComposicao', undefined);
                selectedProducts.value = [];
            }

            // Atualiza os detalhes do produto para uso posterior
            productDetails.value = {
                ...selected,
                nome: StringUtil.utf8Decode(selected.nome),
                descricao: StringUtil.utf8Decode(selected.descricao),
            };
        }
    };

    // Reseta os valores do formulário, imagem e composição ao fechar o diálogo
    const resetDialogValues = () => {
        productDetails.value = {};
        img.value = { ...defaultImgValue };
        selectedProducts.value = [];
    };

    return {
        // Estados e esquema de validação
        isLoading,
        categories,
        imagesSrc,
        products,
        productDetails,
        selectedProducts,
        img,
        disabledButton,
        formSchema,

        // Funções do dropzone e diálogo
        getRootProps,
        getInputProps,
        open,
        dropzoneRest,
        isOpen,
        toggleDialog,
        handleDialogOpen,

        // Funções de API e submissão
        fetchCategories,
        fetchImages,
        fetchProducts,
        handleDefaultImage,
        registerProduct,
        updateProductData,
        uploadImage,
        submitProductData,
        onSubmit,
        handleSubmit,

        // Helpers de manipulação de campos
        handleImageLoad,
        handleUpdateSelect,
        resetDialogValues,
    };
}
