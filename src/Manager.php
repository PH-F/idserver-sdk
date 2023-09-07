<?php

namespace Xingo\IDServer;

use GuzzleHttp\Client;
use Xingo\IDServer\Concerns\CallableResource;
use Xingo\IDServer\Concerns\TokenSupport;
use Xingo\IDServer\Entities;
use Xingo\IDServer\Resources;

/**
 * Class Client
 *
 * @property Resources\Address addresses
 * @method Resources\Address addresses(int | Entities\Address $resource)
 * @property Resources\Company companies
 * @method Resources\Company companies(int | Entities\Company $resource)
 * @property Resources\Reseller resellers
 * @method Resources\Reseller resellers(int | Entities\Reseller $resource)
 * @property Resources\Report reports
 * @method Resources\Report reports(int | Entities\Report $resource)
 * @property Resources\Role roles
 * @method Resources\Role roles(int | Entities\Role $resource)
 * @property Resources\Subscription subscriptions
 * @method Resources\Subscription subscriptions(int | Entities\Subscription $resource)
 * @property Resources\Store stores
 * @method Resources\Store stores(int | Entities\Store $resource)
 * @property Resources\User users
 * @method Resources\User users(int | array | Entities\User ...$resource)
 * @property Resources\MailTemplate mailTemplates
 * @method Resources\MailTemplate mailTemplates(int | array | Entities\User ...$resource)
 * @property Resources\MailLayout mailLayouts
 * @method Resources\MailLayout mailLayouts(int | array | Entities\User ...$resource)
 * @property Resources\DiscountGroup discountGroups
 * @method Resources\DiscountGroup discountGroups(int | array | Entities\User ...$resource)
 * @property Resources\EffortGroup effortGroups
 * @method Resources\EffortGroup effortGroups(int | array | Entities\User ...$resource)
 * @property Resources\Plan plans
 * @method Resources\Plan plans(int | array | Entities\Plan ...$resource)
 * @property Resources\Discount discounts
 * @method Resources\Discount discounts(int | array | Entities\User ...$resource)
 * @property Resources\Effort efforts
 * @method Resources\Effort efforts(int | array | Entities\User ...$resource)
 * @property Resources\Asset assets
 * @method Resources\Asset assets(int | array | Entities\User ...$resource)
 * @property Resources\Variant variants
 * @method Resources\Variant variants(int | array | Entities\User ...$resource)
 * @property Resources\Download downloads
 * @method Resources\Download downloads(int $resource)
 * @property Resources\Invoice invoices
 * @method Resources\Invoice invoices(int $resource)
 * @property Resources\Import imports
 * @method Resources\Import imports(int $resource)
 * @property Resources\Transaction transactions
 * @method Resources\Transaction transactions(int $resource)
 * @property Resources\TransactionItem transactionItems
 * @method Resources\TransactionItem transactionItems(int $resource)
 * @property Resources\Country countries
 * @method Resources\Country countries(int $resource)
 * @property Resources\Coupon coupons
 * @method Resources\Coupon coupons(int $resource)
 * @property Resources\Currency currencies
 * @method Resources\Currency currencies(int $resource)
 * @property Resources\Floating floatings
 * @method Resources\Floating floatings(int $resource)
 * @property Resources\Dunning dunnings
 * @method Resources\Dunning dunnings(int | Entities\Dunning $resource)
 * @property Resources\Duration durations
 * @method Resources\Duration durations(int | Entities\Duration $resource)
 * @property Resources\ShippingCost shippingCosts
 * @method Resources\ShippingCost shippingCosts(int | Entities\ShippingCost $resource)
 * @property Resources\Publisher publishers
 * @method Resources\Publisher publishers(int $resource)
 * @property Resources\OrderItem orderItems
 * @method Resources\OrderItem orderItems(int $resource)
 * @property Resources\VatProductGroup vatProductGroups
 * @method Resources\VatProductGroup vatProductGroups(int | Entities\VatProductGroup $resource)
 * @property Resources\Promotion promotion
 * @method Resources\Promotion promotion(int | Entities\Promotion $resource)
 * @property Resources\VatRate vatRates
 * @method Resources\VatRate vatRates(int | Entities\VatRate $resource)
 * @property Resources\VatRule vatRules
 * @method Resources\VatRule vatRules(int | Entities\VatRule $resource)
 */
class Manager
{
    use CallableResource, TokenSupport;

    /**
     * @var Client
     */
    protected $client;

    /**
     * Manager constructor.
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->setClient($client);
    }

    /**
     * @return Client
     */
    public function client()
    {
        return $this->client;
    }

    /**
     * Set the client to use.
     *
     * @param Client $client
     */
    public function setClient(Client $client)
    {
        $this->client = $client;
    }
}
