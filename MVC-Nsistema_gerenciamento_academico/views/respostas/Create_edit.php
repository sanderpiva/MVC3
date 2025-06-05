<?php

    $isUpdating = isset($respostasData['id_resposta']) && !empty($respostasData['id_resposta']);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Página Web - <?= isset($isUpdating) && $isUpdating ? 'Atualizar' : 'Cadastro'; ?> Respostas</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body class="servicos_forms">

    <div class="form_container">
        <form class="form" action="<?= $isUpdating ? 'index.php?controller=respostas&action=update' : 'index.php?controller=repostas&action=create'; ?>" method="post">
            <h2>Formulário: <?= isset($isUpdating) && $isUpdating ? 'Atualizar' : 'Cadastro'; ?> Respostas</h2>
            <hr>

            <?php if (!empty($errors)): ?>
                <div class="errors" style="color: red;">
                    <?php foreach ($errors as $error): ?>
                        <p><?= htmlspecialchars($error) ?></p>
                    <?php endforeach; ?>
                </div>
                <hr>
            <?php endif; ?>

            <label for="codigoRespostas">Código Respostas:</label>
            <?php if (isset($isUpdating) && $isUpdating): ?>
                <input type="text" name="codigoRespostas" id="codigoRespostas" placeholder="" value="<?= htmlspecialchars($respostaData['codigoRespostas'] ?? '') ?>" required>
                <input type="hidden" name="id_respostas" value="<?= htmlspecialchars($respostaData['id_respostas'] ?? '') ?>">
            <?php else: ?>
                <input type="text" name="codigoRespostas" id="codigoRespostas" placeholder="" value="<?= htmlspecialchars($respostaData['codigoRespostas'] ?? '') ?>" required>
            <?php endif; ?>
            <hr>

            <label for="respostaDada">Resposta Dada:</label>
            <input type="text" name="respostaDada" id="respostaDada" placeholder="" value="<?= htmlspecialchars($respostaData['respostaDada'] ?? '') ?>" required maxlength="1">
            <hr>

            <label>Acertou?</label>
            <div>
                <input type="radio" id="acertouSim" name="acertou" value="1" <?= (isset($respostaData['acertou']) && $respostaData['acertou'] == 1) ? 'checked' : ''; ?> required>
                <label for="acertouSim">Sim</label>
                <input type="radio" id="acertouNao" name="acertou" value="0" <?= (isset($respostaData['acertou']) && $respostaData['acertou'] == 0) ? 'checked' : ''; ?> required>
                <label for="acertouNao">Não</label>
            </div>
            <hr>

            <label for="nota">Nota:</label>
            <input type="number" step="0.01" name="nota" id="nota" placeholder="" value="<?= htmlspecialchars($respostaData['nota'] ?? '') ?>" required min="0">
            <hr>

            <label for="id_questao">Descrição da Questão:</label>
            <?php if (isset($isUpdating) && $isUpdating): ?>
                <input type="text" value="<?= htmlspecialchars($descricaoQuestaoAtual ?? '') ?>" readonly required>
                <input type="hidden" name="id_questao" value="<?= htmlspecialchars($respostaData['Questoes_id_questao'] ?? '') ?>">
            <?php else: ?>
                <select name="id_questao" id="id_questao" required>
                    <option value="">Selecione a descrição da questão</option>
                    <?php foreach ($questoes as $questao): ?>
                        <option value="<?= htmlspecialchars($questao['id_questao']) ?>"
                            <?= (isset($respostaData['id_questao']) && $respostaData['id_questao'] == $questao['id_questao']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($questao['descricao']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
            <hr>

            <label for="id_prova">Código Prova:</label>
            <?php if (isset($isUpdating) && $isUpdating): ?>
                <input type="text" value="<?= htmlspecialchars($codigoProvaAtual ?? '') ?>" readonly required>
                <input type="hidden" name="id_prova" value="<?= htmlspecialchars($respostaData['Questoes_Prova_id_prova'] ?? '') ?>">
            <?php else: ?>
                <select name="id_prova" id="id_prova" required>
                    <option value="">Selecione uma prova</option>
                    <?php foreach ($provas as $prova): ?>
                        <option value="<?= htmlspecialchars($prova['id_prova']) ?>"
                            <?= (isset($respostaData['id_prova']) && $respostaData['id_prova'] == $prova['id_prova']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($prova['codigoProva']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
            <hr>

            <label for="id_disciplina">Disciplina:</label>
            <?php if (isset($isUpdating) && $isUpdating): ?>
                <input type="text" value="<?= htmlspecialchars($nomeDisciplinaAtual ?? '') ?>" readonly required>
                <input type="hidden" name="id_disciplina" value="<?= htmlspecialchars($respostaData['Questoes_Prova_Disciplina_id_disciplina'] ?? '') ?>">
                <hr>
            <?php else: ?>
                <select name="id_disciplina" id="id_disciplina" required>
                    <option value="">Selecione uma disciplina (Professor)</option>
                    <?php foreach ($disciplinas as $disciplina): ?>
                        <?php
                            $professorId = $disciplina['Professor_id_professor'] ?? null;
                            $professorNome = $professorsLookup[$professorId] ?? 'Professor Desconhecido';
                        ?>
                        <option value="<?= htmlspecialchars($disciplina['id_disciplina']) ?>"
                            <?= (isset($respostaData['id_disciplina']) && $respostaData['id_disciplina'] == $disciplina['id_disciplina']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($disciplina['nome']) . ' (' . htmlspecialchars($professorNome) . ')' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
            <hr>

            <label for="id_professor">Professor:</label>
            <?php if (isset($isUpdating) && $isUpdating): ?>
                <input type="text" value="<?= htmlspecialchars($nomeProfessorAtual ?? '') ?>" readonly required>
                <input type="hidden" name="id_professor" value="<?= htmlspecialchars($respostaData['Questoes_Prova_Disciplina_Professor_id_professor'] ?? '') ?>">
                <hr>
            <?php else: ?>
                <select name="id_professor" id="id_professor" required>
                    <option value="">Selecione um professor</option>
                    <?php foreach ($professores as $professor): ?>
                        <option value="<?= htmlspecialchars($professor['id_professor']) ?>"
                            <?= (isset($respostaData['id_professor']) && $respostaData['id_professor'] == $professor['id_professor']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($professor['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
            <hr>

            <label for="id_aluno">Aluno:</label>
            <?php if (isset($isUpdating) && $isUpdating): ?>
                <input type="text" value="<?= htmlspecialchars($nomeAlunoAtual ?? '') ?>" readonly required>
                <input type="hidden" name="id_aluno" value="<?= htmlspecialchars($respostaData['Aluno_id_aluno'] ?? '') ?>">
                <hr>
            <?php else: ?>
                <select name="id_aluno" id="id_aluno" required>
                    <option value="">Selecione um aluno</option>
                    <?php foreach ($alunos as $aluno): ?>
                        <option value="<?= htmlspecialchars($aluno['id_aluno']) ?>"
                            <?= (isset($respostaData['id_aluno']) && $respostaData['id_aluno'] == $aluno['id_aluno']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($aluno['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
            <hr>
            <button type="submit"><?= isset($isUpdating) && $isUpdating ? 'Atualizar' : 'Cadastrar'; ?></button>
        </form>

        <hr>
        <?php if (isset($isUpdating) && $isUpdating): ?>
            <a href="index.php?controller=resposta&action=list">Voltar à lista</a>
        <?php endif; ?>
    </div>
    <a href="index.php?controller=professor&action=showServicesPage">Serviços</a>
    <hr>
</body>
<footer>
    <p>Desenvolvido por Juliana e Sander</p>
</footer>
</html>