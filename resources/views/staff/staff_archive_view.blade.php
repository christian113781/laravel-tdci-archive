@extends('staff.staff_dashboard')
<style>
    .table-border-thin {
        border-collapse: collapse;
    }

    .table-border-thin th,
    .table-border-thin td {
        border: 1.5px solid #dee2e6;
        /* 1px light grey border */
    }

    table th {
        white-space: nowrap;
    }

    table td {
        text-align: justify;
        word-spacing: 5px
    }

    .file-attach {
        display: inline-block;
        /* removed vw- prefix from your original class so it’s simpler and matches HTML */
    }

    .file-title {
        background: #f9f9f9;
        border: 1px solid #ddd;
        padding: 8px;
    }

    .file-body {
        border-right: 1px solid #ddd;
        border-bottom: 1px solid #ddd;
        border-left: 1px solid #ddd;
        padding: 10x;
    }

    .file-icon {
        font-size: 40px;
        padding: 10px;
    }
</style>
@section('archive')
    <div class="page-inner">
        <div class="page-header">
            <h3 class="fw-bold mb-3">Archives</h3>
            <ul class="breadcrumbs mb-3">
                <li class="nav-home">
                    <a href="#">
                        <i class="icon-home"></i>
                    </a>
                </li>
                <li class="separator">
                    <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                    <a href="#">Manage</a>
                </li>
                <li class="separator">
                    <i class="icon-arrow-right"></i>
                </li>
                <li class="nav-item">
                    <a href="#">Mange Archive</a>
                </li>
            </ul>
        </div>
        <div class="row">
            <div class="col-md-10">
                <table class="table  table-border-thin">
                    <tr>
                        <th>Authors</th>
                        <td>Fulo, Vianney Lois T.</td>
                    </tr>
                    <tr>
                        <th>Title</th>
                        <td>Pagsibol ng himig: nurturing the musical creativity of women and female-identifying
                            underground musicians through a gendered approach in music training and performance
                            venues</td>
                    </tr>
                    <tr>
                        <th>Year Issued</th>
                        <td>2025</td>
                    </tr>
                    <tr>
                        <th>Subject</th>
                        <td>As the visibility of women and female-identifying musicians in the local underground
                            music scene increases, new perspectives must emerge to transform how the space is
                            experienced. Music infrastructure, such as performance and training spaces, must
                            foster creativity while also adapting to meet the needs of both the target
                            demographic and the public without becoming stagnant or constrained by conventional
                            interpretations of a 'music space'. Hence, this study aims to holistically explore
                            the ways in which gendered creative spaces and an application of the Musical
                            Creativity Model (Burnard, 2012) could improve the experience within music spaces by
                            increasing accessibility, fostering individual creativity, and challenging the
                            typical denotations of training and performance venues. By applying the principles
                            of gendered spaces and examining the nature of musical creativity in spatial
                            development, this study finds that blending the formal (theaters and recording
                            rooms) with the informal (busking areas and adaptable hallways), while enhancing the
                            adaptability of the space, encourages the public to engage with the space more
                            freely. Using the musical principle of syncopation-which emphasizes rhythm,
                            disruption, and unexpected accents-the project aims to explore the intersection of
                            music and architecture, creating a space where both aspiring and established
                            musicians, along with their audiences, can engage with full accessibility and a deep
                            respect for creativity and individuality.</td>
                    </tr>
                    <tr>
                        <th>Program</th>
                        <td>Bachelor of Science in Architecture</td>
                    </tr>
                    <tr>
                        <th>Language</th>
                        <td>English</td>
                    </tr>
                    <tr>
                        <th>Keyword</th>
                        <td>Gender identity--Music; Music and architecture; Women musicians</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>Thesis/Dissertation</td>
                    </tr>
                </table>
            </div>

            <div class="col-md-">
                <div class="row mb-5">
                    <div class="file-attach p-10 col-md-12 text-center mb-4">
                        <div class="file-title bg-white">
                            Thesis File
                        </div>
                        <div class="file-body bg-white">
                            <div class="file-icon">
                                <a href="https://digitalarchives.upd.edu.ph/file/172617" target="_blank" rel="noopener">
                                    <i class="fas fa-file-pdf text-danger"></i>
                                </a>
                            </div>
                            <div class="file-caption">
                                7.23 Mb
                            </div>
                        </div>
                    </div>

                    <div class="file-attach p-10 col-md-12 text-center mb-4">
                        <div class="file-title bg-white">
                            Tables File
                        </div>
                        <div class="file-body bg-white">
                            <div class="file-icon">
                                <a href="https://digitalarchives.upd.edu.ph/file/172617" target="_blank" rel="noopener">
                                    <i class="fas fa-file-pdf text-danger"></i>
                                </a>
                            </div>
                            <div class="file-caption">
                                7.23 Mb
                            </div>
                        </div>
                    </div>

                    <div class="file-attach p-10 col-md-12 text-center mb-4">
                        <div class="file-title bg-white">
                            Figures File
                        </div>
                        <div class="file-body bg-white">
                            <div class="file-icon">
                                <a href="https://digitalarchives.upd.edu.ph/file/172617" target="_blank" rel="noopener">
                                    <i class="fas fa-file-pdf text-danger"></i>
                                </a>
                            </div>
                            <div class="file-caption">
                                7.23 Mb
                            </div>
                        </div>
                    </div>


                    <div class="file-attach p-10 col-md-12 text-center">
                        <div class="file-title bg-white">
                            Recommendation File
                        </div>
                        <div class="file-body bg-white">
                            <div class="file-icon">
                                <a href="https://digitalarchives.upd.edu.ph/file/172617" target="_blank" rel="noopener">
                                    <i class="fas fa-file-pdf text-danger"></i>
                                </a>
                            </div>
                            <div class="file-caption">
                                7.23 Mb
                            </div>
                        </div>
                    </div>


                    


                    
                </div>
            </div>




            <div class="col-md-12">
                <div class="bg-white p-3 mb-3">
                    <div class="row">
                        <div class="col-xs-6">
                            <div><span><i>Category</i> : </span><span class=""><span
                                        class="txt-info"><strong>F</strong> -
                                        Regular work, i.e., it has no patentable
                                        invention or creation, the author does not wish for personal publication, there is
                                        no confidential information.</span></span></div>
                            <div>&nbsp;</div>
                            <div><span><i>Access Permission</i> : </span><span class=""><span
                                        class="txt-info"><strong>Open Access</strong></span></span></div>
                        </div>
                        <div class="col-xs-6">
                        </div>
                    </div>

                </div>

            </div>



            <div class="col-md-12">
                <div class="bg-white p-3">
                    <div class="">
                        dsadasd
                    </div>
                </div>
            </div>
        </div>
    </div>




    </div>

    </div>
@endsection


@push('archive-script')
@endpush
