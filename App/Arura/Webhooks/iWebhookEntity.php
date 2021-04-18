<?php
namespace Arura\Webhooks;

interface iWebhookEntity{
    public function serialize(): array;
    public function TriggerWebhook(int $trigger, array $data);
}