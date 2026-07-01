<?php

declare(strict_types=1);

namespace Modules\Cron\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Cron\DTOs\CronJobDTO;
use Modules\Cron\Http\Requests\StoreCronJobRequest;
use Modules\Cron\Http\Requests\UpdateCronJobRequest;
use Modules\Cron\Models\CronJob;
use Modules\Cron\Services\CronService;

class CronJobController extends CoreController
{
    public function __construct(
        private readonly CronService $cronService,
    ) {}

    public function index(): View
    {
        return view('cron::cron-jobs.index', [
            'jobs' => CronJob::orderBy('name')->paginate(20),
        ]);
    }

    public function create(): View
    {
        return view('cron::cron-jobs.create', [
            'job' => new CronJob,
        ]);
    }

    public function store(StoreCronJobRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        CronJob::create($data);

        return redirect()->route('admin.cron-jobs.index')
            ->with('success', __('cron::messages.created'));
    }

    public function edit(CronJob $cronJob): View
    {
        return view('cron::cron-jobs.edit', [
            'job' => $cronJob,
        ]);
    }

    public function update(UpdateCronJobRequest $request, CronJob $cronJob): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');
        $cronJob->update($data);

        return redirect()->route('admin.cron-jobs.index')
            ->with('success', __('cron::messages.updated'));
    }

    public function destroy(CronJob $cronJob): RedirectResponse
    {
        $cronJob->delete();

        return redirect()->route('admin.cron-jobs.index')
            ->with('success', __('cron::messages.deleted'));
    }

    public function run(CronJob $cronJob): RedirectResponse
    {
        $this->cronService->runJob($cronJob);

        return redirect()->route('admin.cron-jobs.index')
            ->with('success', __('cron::messages.executed'));
    }
}
