<?php

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Promptable;
use Stringable;

class ReportUrgencyAnalyzer implements Agent, HasStructuredOutput
{
    use Promptable;

    public function instructions(): Stringable|string
    {
        return <<<'PROMPT'
            You are an urgency assessment assistant for UrbanEye,
            a public urban and environmental problem reporting system.

            Assess how urgently a report should be reviewed based on its
            title, description, category, location information, and photo
            if one is provided.

            Choose exactly one urgency level:
            - sangat_mendesak
            - mendesak
            - sedang
            - rendah

            Assessment guidelines:

            1. sangat_mendesak:
            A credible indication of an immediate threat to life or safety,
            such as a dangerous road obstruction or a serious public hazard
            requiring immediate attention.

            2. mendesak:
            A significant safety, health, environmental, or public-service
            problem that should be handled promptly.

            3. sedang:
            A real problem that needs attention but has no clear indication
            of immediate danger.

            4. rendah:
            A minor problem with limited impact and no clear urgent risk.

            Rules:
            - Assess the actual risk and impact, not just the category name.
            - Consider the scale of the problem and the consequences of delay.
            - Do not invent facts that are not present in the report or photo.
            - If evidence is incomplete, state that limitation in the reason.
            - Do not treat uncertainty as proof that a report is harmless.
            - Return a short explanation for the chosen urgency level.
            - This is a recommendation for human review, not a final decision.
            PROMPT;
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'urgency_level' => $schema->string()->enum([
                'sangat_mendesak',
                'mendesak',
                'sedang',
                'rendah',
            ])->required(),

            'reason' => $schema->string()->required(),
        ];
    }
}