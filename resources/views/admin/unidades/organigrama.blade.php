@extends('layouts.app')

@section('content')

    {{-- Estilos Treant --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/treant-js/1.0/Treant.css" />
    <style>
        /* Contenedor del organigrama */
        #tree-container {
            width: 100%;
            height: 80vh;
            overflow: auto;
            border: 1px solid #ddd;
            background: var(--bs-body-bg);
            border-radius: 10px;
            padding: 20px;
        }

        /* Nodos: tamaño fijo */
        .node {
            background: var(--bs-body-bg);
            border: 1px solid var(--bs-border-color);
            border-radius: 8px;
            padding: 10px 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, .15);
            text-align: center;
            width: 280px;
            /* ancho fijo */
            height: 100px;
            /* alto fijo */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            word-wrap: break-word;
        }

        .node-title {
            font-weight: bold;
            font-size: 16px;
        }

        .node-jefe {
            font-size: 13px;
            opacity: .7;
        }

        /* Botón volver */
        .back-button {
            margin-bottom: 15px;
        }

        /* Responsivo: nodos siempre mismo tamaño */
        @media (max-width: 768px) {
            .node {
                width: 280px;
                height: 100px;
            }
        }
    </style>

    <div class="d-flex justify-content-between align-items-center mb-5">
        <h3 class="mb-3">
            {{ $unidadId ? 'Organigrama de la Unidad: ' . $tree[0]['nombre'] : 'Organigrama General' }}
        </h3>

        <div class="d-flex gap-2">
            <a href="{{ route('admin.unidades.index') }}" class="btn btn-outline-primary d-flex align-items-center gap-2">
                <i data-feather="arrow-left"></i>
                <span class="d-none d-md-inline">Volver</span>
            </a>
        </div>
    </div>
    <!-- row  -->
    <div class="row">
        <div class="col-xl-12 col-12 mb-5">
            {{-- Organigrama --}}
            <div id="tree-container"></div>
        </div>
    </div>
@endsection

@section('scripts')
    {{-- Dependencias Treant --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.3.0/raphael.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/treant-js/1.0/Treant.min.js"></script>

    <script>
        const treeData = @json($tree);

        function convertToTreant(node) {
            let structure = {
                text: {
                    name: node.nombre,
                    title: node.jefe ? node.jefe : "Sin jefe"
                },
                HTMLclass: "node"
            };

            if (node.children && node.children.length > 0) {
                structure.children = node.children.map(child => convertToTreant(child));
            }

            return structure;
        }

        const treantStructure = {
            chart: {
                container: "#tree-container",
                connectors: { type: 'step' },
                node: { collapsable: true },
                levelSeparation: 50,
                siblingSeparation: 25,
                subTeeSeparation: 35,
                animateOnInit: true,
                animation: {
                    nodeAnimation: "easeOutBounce",
                    nodeSpeed: 500,
                    connectorsAnimation: "bounce",
                    connectorsSpeed: 500
                }
            },
            nodeStructure: convertToTreant(treeData[0])
        };

        new Treant(treantStructure);
    </script>

@endsection