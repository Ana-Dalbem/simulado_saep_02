<?php
require_once 'config.php';
verificarLogin();

$mensagem = $tipoMensagem = '';

if (isset($_GET['excluir'])){
    try{
        $conn->prepare("DELETE FROM produtos WHERE id = :id")
            ->execute(['id' => $_GET['excluir']]);
        $mensagem = 'Produto excluído com sucesso!';
        $tipoMensagem = 'succeso';
    } catch(PDOException $e) {
        $mensagem = 'Erro ao excluir produto: ' . $e->getMessage();
        $tipoMensagem = 'error';
    }
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $id = $_POST['id'] ?? 0;
    $data = [
        'codigo'=> trim($_POST['codigo'] ?? ''),
        'nome'=> trim($_POST['nome'] ?? ''),
        'fabricante'=> trim($_POST['fabricante'] ?? ''),
        'preco'=> trim($_POST['preco'] ?? ''),
        'estoque_atual'=> trim($_POST['estoque_atual'] ?? 0),
        'estoque_minimo'=> trim($_POST['estoque_minimo'] ?? 0),
        'ativo'=>trim($_POST['ativo'] ?? 1),
        'data_validade'=>trim($_POST['data_validade'] ?? date('Y-m-d')),
        'peso'=>trim($_POST['peso'] ?? 0),
        'categoria'=>trim($_POST['categoria'] ?? null),
        'tamanho'=>trim($_POST['tamanho'] ?? null),
        'material_fabricacao'=>trim($_POST['material_fabricacao'] ?? null),
        'instrucoes_uso'=>trim($_POST['instrucoes_uso'] ?? null)
    ];
    if(!$data['codigo'] || !$data['nome'] || $data['preco']<=0){
        $mensagem = "Preencha todos os campos obrigatórios.";
        $tipoMensagem = "error";
    }else{
        if($id > 0){            
            $sql = "UPDATE produtos SET codigo = :codigo,
             nome = :nome, 
             fabricante = :fabricante,
             preco = :preco,
             estoque_atual = :estoque_atual,
             estoque_minimo = :estoque_minimo,
             ativo = :ativo,
             data_validade = :data_validade,
             peso = :peso,
             categoria = :categoria,
             tamanho = :tamanho,
             material_fabricacao = :material_fabricacao,
             instrucoes_uso = :instrucoes_uso WHERE id = :id";
             $data['id'] = $id;
        }else{
            $sql = "INSERT INTO produtos(
             codigo,
             nome,
             fabricante,
             preco,
             estoque_atual,
             estoque_minimo,
             ativo,
             data_validade,
             peso, 
             categoria,
             tamanho,
             material_fabricacao,
             instrucoes_uso) VALUES (:codigo, 
             :nome,
             :fabricante,
             :preco,
             :estoque_atual,
             :estoque_minimo,
             :ativo, 
             :data_validade,
             :peso,
             :categoria,
             :tamanho,
             :material_fabricacao,
             :instrucoes_uso)";
        }
        try{            
            $conn->prepare($sql)->execute($data);
            $mensagem = $id > 0 ? "Produto atualizado com sucesso!" : "Produto cadastrado com sucesso!";
            $tipoMensagem = "successo";
        }catch(PDOException $e){            
            $mensagem = "Erro ao salvar produto: " . $e->getMessage();
            $tipoMensagem = "error";
        }
    
    }
}
$produtoEditar = null;
if(isset($_GET['editar'])){
    $stmt = $conn->prepare("SELECT * FROM produtos WHERE id = :id");
    $stmt->execute([':id'=>$_GET['editar']]);
    $produtoEditar = $stmt->fetch();
}

$busca = trim($_GET['busca'] ?? '');
$sql = "SELECT * FROM produtos WHERE ativo = 1" . ($busca ? " AND (nome LIKE :b OR codigo LIKE :b)" : "") . " ORDER BY nome";
$stmt = $conn->prepare($sql);
$stmt->execute($busca ? ['b' => "%$busca%"] : []);
$produtos = $stmt->fetchAll();


$ativos = [1=>'Ativo', 2=>'Inativo'];

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Produtos - Indústria</title>
</head>
  <body>
    <h1>Cadastro de Produtos - Indústria</h1>
    <p><a href="index.php">Voltar </a></p>
    <hr>

    <?php if($mensagem): ?>
        <p style="color: <?= $tipoMensagem == 'successo' ? 'green' : 'red'; ?>"><?= htmlspecialchars($mensagem); ?></p>
    <?php endif; ?>

    <h2><?php echo $produtoEditar ? "Editar Produto" : "Novo"; ?> Produto </h2>
    <form method= "POST">
        <input type="hidden" name="id" value="<?php echo $produtoEditar['id'] ?? ''; ?>">
        <label> Código: <input type="text" name="codigo" required value="<?php echo htmlspecialchars($produtoEditar['codigo'] ?? ''); ?>" ></label><br>
        <label> Nome: <input type="text" name="nome" required value="<?php echo htmlspecialchars($produtoEditar['nome'] ?? ''); ?>" size="40"></label><br>
        <label> Fabricante: <input type="text" name="fabricante" value="<?php echo htmlspecialchars($produtoEditar['fabricante'] ?? ''); ?>" ></label><br>
        <label> Preço: <input type="number" step="0.01" name="preco" required value="<?php echo htmlspecialchars($produtoEditar['preco'] ?? ''); ?>" ></label><br>
        <label> Estoque Atual: <input type="number" min="0" name="estoque_atual" value="<?php echo htmlspecialchars($produtoEditar['estoque_atual'] ?? 0); ?>" ></label><br>
        <label> Estoque Mínimo: <input type="number" min="0" name="estoque_minimo" value="<?php echo htmlspecialchars($produtoEditar['estoque_minimo'] ?? 0); ?>" ></label><br>
        <label>Status (Ativo): <select name="ativo">
                <option value="1" <?= ($produtoEditar['ativo'] ?? 1) == 1 ? 'selected' : '' ?>>Ativo</option>
                <option value="0" <?= ($produtoEditar['ativo']?? 1) == 0 ? 'selected' : '' ?>>Inativo</option>
            </select>
        </label>
        <label> Data Validade: <input type="date" name="data_validade" value="<?php echo htmlspecialchars($produtoEditar['data_validade'] ?? date('Y-m-d')); ?>" ></label><br>
        <label> Peso: <input type="number" step="0.01" name="peso" value="<?php echo htmlspecialchars($produtoEditar['peso'] ?? 0.00); ?>" ></label><br>
        
        
       
        <label>Categoria: <input type="text" name="categoria" size="40" value="<?php echo htmlspecialchars($produtoEditar['categoria'] ?? ''); ?>" ></label><br>
        <label> Tamanho: <input type="text" name="tamanho" value="<?php echo htmlspecialchars($produtoEditar['tamanho'] ?? ''); ?>" ></label><br>
        <label> Material Fabricação: <input type="text" name="material_fabricacao" value="<?php echo htmlspecialchars($produtoEditar['material_fabricacao'] ?? ''); ?>" ></label><br>
        <label> Instruções de Uso: <input type="text" name="instrucoes_uso" value="<?php echo htmlspecialchars($produtoEditar['instrucoes_uso'] ?? ''); ?>" ></label><br>

        <button type="submit">Salvar</button>
        <?php if($produtoEditar): ?>
            <a href="produtos.php"></a><button type="button">Cancelar</button><?php endif; ?>
        
    </form>
    <hr>
    <h2>Lista de Produtos</h2>
    <form method="GET">
        <label>Buscar: 
        <input type="text" name="busca"  value="<?php echo htmlspecialchars($busca); ?>" placeholder="Nome, Código ou Categoria...">
        </label>
    <button type="submit">Buscar</button>
    <?php if($busca): ?>
        <a href="produtos.php">Limpar</a>
    <?php endif; ?>
    </form>
    <br>

    <?php if($produtos): ?>
        <table border="1" cellpadding="5" cellspacing="0">
            <tr>
                <th>Código</th>
                <th>Nome</th>
                <th>Fabricante</th>
                <th>Categoria</th>
                <th>Preço</th>
                <th>Estoque Atual</th>
                <th>Estoque Mínimo</th>
                <th>Status</th>
                <th>Data Validade</th>
                <th>Peso</th>
                <th>Tamanho</th>
                <th>Material Fabricação</th>
                <th>Instruções de Uso</th>
    </tr>
    <?php foreach ($produtos as $p): ?>
               <tr>
                    <td><?= htmlspecialchars($p['codigo']) ?></td>
                    <td><?= htmlspecialchars($p['nome']) ?></td>
                    <td><?= htmlspecialchars($p['fabricante'] ?? '') ?></td>
                    <td><?= htmlspecialchars($p['categoria'] ?? '') ?></td>
                    <td>R$ <?= number_format($p['preco'], 2, ',', '.') ?></td>
                    <td><?= $p['estoque_atual'] ?></td>
                    <td><?= $p['estoque_minimo'] ?></td>
                    <td><?= $ativo[$p['ativo']] ?? '-' ?></td>
                    <td><?= $p['data_validade'] ? date('d/m/Y', strtotime($p['data_validade'])) : '-' ?></td>
                    <td><?= $p['peso'] ?> kg</td>
                    <td><?= htmlspecialchars($p['tamanho'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($p['material_fabricacao'] ?? '-') ?></td>
                    <td><?= htmlspecialchars($p['instrucoes_uso'] ?? '-') ?></td>
                    <td>
                        <a href="produtos.php?editar=<?= $p['id'] ?>">Editar</a>
                        <a href="produtos.php?excluir=<?= $p['id'] ?>" onclick="return confirm('Deseja realmente excluir este produto?')">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>Nenhum produto cadastrado.</p>
    <?php endif; ?>
        
    
 </body>
 </html>
