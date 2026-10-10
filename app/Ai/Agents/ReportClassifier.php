<?php

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Messages\Message;
use Laravel\Ai\Promptable;
use Laravel\Ai\Providers\Tools\ProviderTool;
use Stringable;

class ReportClassifier implements Agent, HasStructuredOutput, HasTools
{
    use Promptable;

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
       return <<<'PROMPT'
You are a classification assistant for UrbanEye.

Your task is to classify an uploaded photo of an urban or environmental problem.

Choose only one category from these categories:
- Sampah
- Jalan Rusak
- Drainase
- Fasilitas Umum
- Lampu Jalan
- Ruang Terbuka

Only classify the image if it clearly shows an urban or environmental problem.

If the image is not a photo of an urban or environmental problem, set:
- category: "Tidak Teridentifikasi"
- confidence: 0
- reason: explain briefly why the image cannot be classified.

Do not create any category other than the categories above or "Tidak Teridentifikasi".

For valid images:
- category: the selected category
- confidence: confidence level from 0 to 100
- reason: a short explanation for the classification
PROMPT;
    }

    /**
     * Get the tools available to the agent.
     *
     * @return list<Agent|Tool|ProviderTool>
     */
    public function tools(): iterable
    {
        return [];
    }

    /**
     * Get the agent's structured output schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'category' => $schema->string()->required(),
            'confidence' => $schema->number()->required(),
            'reason' => $schema->string()->required(),
        ];
    }
}