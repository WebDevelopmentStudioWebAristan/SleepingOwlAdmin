<?php

use Mockery as m;
use SleepingOwl\Admin\Templates\TemplateDefault;

class TemplateDefaultTest extends TestCase
{
    public function tearDown()
    {
        m::close();
    }

    /**
     * @return TemplateDefault
     */
    protected function getTemplate()
    {
        return new TemplateDefault();
    }

    /**
     * @covers TemplateDefault::getViewNamespace
     */
    public function test_getViewNamespace()
    {
        $this->assertEquals('sleeping_owl::', $this->getTemplate()->getViewNamespace());
    }

    /**
     * @covers TemplateDefault::getViewPath
     */
    public function test_getViewPath()
    {
        $this->assertEquals('sleeping_owl::default.test', $this->getTemplate()->getViewPath('test'));

        $view = m::mock(\Illuminate\View\View::class);
        $view->shouldReceive('getPath')->once()->andReturn('custom.template');
        $this->assertEquals('custom.template', $this->getTemplate()->getViewPath($view));
    }

    /**
     * @covers TemplateDefault::view
     * @covers TemplateDefault::getViewPath
     */
    public function test_view()
    {
        $this->getViewMock()->shouldReceive('make')->once()->withArgs([
            'sleeping_owl::default.test', ['test'], [],
        ])->andReturn('html');

        $this->assertEquals('html', $this->getTemplate()->view(
            'test', ['test']
        ));

        $this->tearDown();

        $view = m::mock(\Illuminate\View\View::class);

        $view->shouldReceive('with')->with(['test'])->once()->andReturnSelf();

        $this->assertEquals($view, $this->getTemplate()->view($view, ['test']));
    }

    /**
     * @covers TemplateDefault::getTitle
     */
    public function test_getTitle()
    {
        $this->getConfigMock()
            ->shouldReceive('get')
            ->with('sleeping_owl.title', null)
            ->once()
            ->andReturn('Hello world');

        $this->assertEquals('Hello world', $this->getTemplate()->getTitle());
    }

    /**
     * @covers TemplateDefault::makeTitle
     */
    public function test_makeTitle()
    {
        $this->getConfigMock()
            ->shouldReceive('get')
            ->with('sleeping_owl.title', null)
            ->twice()
            ->andReturn('Hello world');

        $this->assertEquals('Hello world', $this->getTemplate()->makeTitle(''));
        $this->assertEquals('Hello world', $this->getTemplate()->makeTitle(null));

        // -----------

        $this->getConfigMock()
            ->shouldReceive('get')
            ->with('sleeping_owl.title', null)
            ->once()
            ->andReturn('Hello world');

        $this->assertEquals('Title | Hello world', $this->getTemplate()->makeTitle('Title'));

        // -----------

        $this->getConfigMock()
            ->shouldReceive('get')
            ->with('sleeping_owl.title', null)
            ->once()
            ->andReturn('Hello world');

        $this->assertEquals('Title -> Hello world', $this->getTemplate()->makeTitle('Title', ' -> '));
    }

    /**
     * @covers TemplateDefault::getLogo
     */
    public function test_getLogo()
    {
        $this->getConfigMock()
            ->shouldReceive('get')
            ->with('sleeping_owl.logo', null)
            ->once()
            ->andReturn($logo = '<img src="logo.png" />');

        $this->assertEquals($logo, $this->getTemplate()->getLogo());
    }

    /**
     * @covers TemplateDefault::getLogoMini
     */
    public function test_getLogoMini()
    {
        $this->getConfigMock()
            ->shouldReceive('get')
            ->with('sleeping_owl.logo_mini', null)
            ->once()
            ->andReturn($logo = '<img src="logo-mini.png" />');

        $this->assertEquals($logo, $this->getTemplate()->getLogoMini());
    }

    /**
     * @covers TemplateDefault::renderBreadcrumbs
     */
    public function test_renderBreadcrumbs()
    {
        $this->getConfigMock()
            ->shouldReceive('get')
            ->with('sleeping_owl.breadcrumbs', null)
            ->once()
            ->andReturn(true);

        $this->getBreadcrumbsMock()
            ->shouldReceive('renderIfExists')
            ->with('test')
            ->once()
            ->andReturn($return = '<li />');

        $this->assertEquals($return, $this->getTemplate()->renderBreadcrumbs('test'));

        // -----------

        $this->getConfigMock()
            ->shouldReceive('get')
            ->with('sleeping_owl.breadcrumbs', null)
            ->once()
            ->andReturn(false);

        $this->getBreadcrumbsMock()
            ->shouldNotReceive('renderIfExists');

        $this->assertNull($this->getTemplate()->renderBreadcrumbs('test'));
    }
}